<?php

namespace Database\Seeders;

use App\Models\Formulario;
use App\Models\FormularioEnvio;
use App\Models\FormularioFatorSatisfacao;
use App\Models\FormularioPergunta;
use App\Models\FormularioResposta;
use App\Models\FormularioRespostaTratada;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DistincaoRespostasDemoSeeder extends Seeder
{
    /** Envios completos por questionário (cada um responde todos os segmentos do form). */
    public static int $enviosPorFormulario = 95;

    /** Remove apenas envios e respostas antes de popular (não apaga formulários). */
    public static bool $limparRespostasAntes = true;

    /** Cria entrevistadores fictícios se houver poucos usuários. */
    public static bool $criarAplicadores = true;

    private array $demo;

    /** @var array<string, int> titulo fator => id */
    private array $fatoresPorFormulario = [];

    /** @var list<User> */
    private array $aplicadores = [];

    private const GENEROS = ['Masculino', 'Feminino', 'Outro'];

    private const IDADES = [
        '18 a 28 anos',
        '29 a 39 anos',
        '40 a 50 anos',
        '51 a 61 anos',
        'Mais de 61 anos',
    ];

    public function run(): void
    {
        $this->demo = require database_path('data/distincao_demo_bage.php');

        $totalFormularios = Formulario::count();
        if ($totalFormularios === 0) {
            $this->command?->error('Nenhum formulário encontrado. Rode antes: php artisan db:seed --class=DistincaoEmpresarialSeeder');

            return;
        }

        DB::transaction(function () use ($totalFormularios) {
            if (self::$limparRespostasAntes) {
                $this->limparRespostas();
                $this->command?->warn('Respostas e envios de demonstração removidos.');
            }

            $this->prepararAplicadores();
            $this->carregarFatores();

            $totalEnvios = 0;
            $totalRespostas = 0;

            Formulario::query()
                ->orderBy('id')
                ->each(function (Formulario $formulario) use (&$totalEnvios, &$totalRespostas) {
                    [$envios, $respostas] = $this->popularFormulario($formulario);
                    $totalEnvios += $envios;
                    $totalRespostas += $respostas;
                    $this->command?->info(sprintf(
                        'Formulário #%d "%s": %d envios, %d respostas.',
                        $formulario->id,
                        Str::limit($formulario->titulo, 50),
                        $envios,
                        $respostas
                    ));
                });

            $this->command?->info("Demo concluída: {$totalEnvios} envios e {$totalRespostas} respostas em {$totalFormularios} formulário(s).");
            $this->command?->line('Relatórios: classificação, aplicadores, bairros e dashboard devem exibir dados.');
        });
    }

    private function limparRespostas(): void
    {
        DB::table('formulario_respostas_tratadas')->delete();
        DB::table('formulario_respostas')->delete();
        DB::table('formulario_envios')->delete();
    }

    private function prepararAplicadores(): void
    {
        if (self::$criarAplicadores) {
            $this->criarAplicadoresDemo();
        }

        $usuarios = User::all();

        if ($usuarios->isEmpty()) {
            throw new \RuntimeException('Nenhum usuário no banco. Rode UserSeeder primeiro.');
        }

        $this->aplicadores = $usuarios->all();
    }

    private function criarAplicadoresDemo(): void
    {
        $nomes = [
            ['name' => 'Rita Jorge', 'email' => 'rita.aplicadora@demo.local'],
            ['name' => 'Carlos Campo', 'email' => 'carlos.campo@demo.local'],
            ['name' => 'Maria Pesquisa', 'email' => 'maria.pesquisa@demo.local'],
            ['name' => 'Pedro ACIBA', 'email' => 'pedro.aciba@demo.local'],
            ['name' => 'Ana Bagé', 'email' => 'ana.bage@demo.local'],
            ['name' => 'Lucas Entrevistador', 'email' => 'lucas.entrevista@demo.local'],
            ['name' => 'Juliana Fronteira', 'email' => 'juliana.fronteira@demo.local'],
        ];

        $senha = Hash::make('demo-aplicador');

        foreach ($nomes as $dados) {
            User::updateOrCreate(
                ['email' => $dados['email']],
                [
                    'name' => $dados['name'],
                    'password' => $senha,
                    'is_admin' => false,
                    'ativo' => true,
                ]
            );
        }

        $this->command?->info('Aplicadores demo criados (@demo.local — senha: demo-aplicador).');
    }

    private function carregarFatores(): void
    {
        $this->fatoresPorFormulario = [];

        foreach (Formulario::pluck('id') as $formularioId) {
            $map = [];
            foreach (FormularioFatorSatisfacao::where('formulario_id', $formularioId)->get() as $fator) {
                $map[mb_strtolower(trim($fator->titulo))] = $fator->id;
            }
            $this->fatoresPorFormulario[$formularioId] = $map;
        }
    }

    /**
     * @return array{0: int, 1: int} [envios, respostas]
     */
    private function popularFormulario(Formulario $formulario): array
    {
        $passos = $formulario->passos()
            ->with(['perguntas.opcoes'])
            ->orderBy('ordem')
            ->get();

        $fatores = $this->fatoresPorFormulario[$formulario->id] ?? [];
        $envios = 0;
        $respostas = 0;
        $adminId = User::where('is_admin', true)->value('id') ?? $this->aplicadores[0]->id;

        for ($e = 0; $e < self::$enviosPorFormulario; $e++) {
            $aplicador = $this->aplicadores[array_rand($this->aplicadores)];
            $local = $this->demo['bairros'][array_rand($this->demo['bairros'])];
            $jitterLat = (mt_rand(-80, 80) / 100000);
            $jitterLng = (mt_rand(-80, 80) / 100000);

            $fim = Carbon::now('America/Sao_Paulo')
                ->subDays(mt_rand(0, 75))
                ->subHours(mt_rand(0, 12))
                ->subMinutes(mt_rand(0, 59));
            $duracao = mt_rand(420, 2400);
            $inicio = $fim->copy()->subSeconds($duracao);

            $envio = FormularioEnvio::create([
                'formulario_id' => $formulario->id,
                'usuario_id' => $aplicador->id,
                'ip' => '177.'.mt_rand(10, 250).'.'.mt_rand(1, 254).'.'.mt_rand(1, 254),
                'user_agent' => 'DistincaoRespostasDemoSeeder/1.0',
                'geo_info' => [
                    'city' => $this->demo['cidade'],
                    'region' => $this->demo['estado'],
                    'country' => 'BR',
                ],
                'latitude' => $local['lat'] + $jitterLat,
                'longitude' => $local['lng'] + $jitterLng,
                'rua' => $local['rua'],
                'bairro' => $local['bairro'],
                'cidade' => $this->demo['cidade'],
                'estado' => $this->demo['estado'],
                'inicio_resposta' => $inicio,
                'fim_resposta' => $fim,
                'duracao_em_segundos' => $duracao,
                'created_at' => $fim,
                'updated_at' => $fim,
            ]);

            $envios++;

            foreach ($passos as $passo) {
                foreach ($passo->perguntas as $pergunta) {
                    if ($pergunta->usa_fatores_satisfacao) {
                        $segmento = $this->extrairSegmentoDoPasso($passo->titulo);
                        [$textoResposta, $fatorId] = $this->gerarRespostaSegmento($segmento, $fatores);
                    } else {
                        [$textoResposta, $fatorId] = $this->gerarRespostaDemografica($pergunta);
                    }

                    $resposta = FormularioResposta::create([
                        'formulario_envio_id' => $envio->id,
                        'pergunta_id' => $pergunta->id,
                        'usuario_id' => $aplicador->id,
                        'resposta' => $textoResposta,
                        'fator_id' => $fatorId,
                        'input_fator' => '',
                        'created_by' => $adminId,
                        'updated_by' => $adminId,
                        'created_at' => $fim,
                        'updated_at' => $fim,
                    ]);

                    FormularioRespostaTratada::create([
                        'resposta_id' => $resposta->id,
                        'resposta_tratada' => $textoResposta,
                        'created_by' => $adminId,
                        'updated_by' => $adminId,
                        'created_at' => $fim,
                        'updated_at' => $fim,
                    ]);

                    $respostas++;
                }
            }
        }

        return [$envios, $respostas];
    }

    private function extrairSegmentoDoPasso(string $tituloPasso): string
    {
        if (preg_match('/\d+\.\s+(.+?)\s+\([^)]+\)/u', $tituloPasso, $m)) {
            return trim($m[1]);
        }

        return trim($tituloPasso);
    }

    /**
     * @param  array<string, int>  $fatores
     * @return array{0: string, 1: int|null}
     */
    private function gerarRespostaSegmento(string $segmento, array $fatores): array
    {
        $roll = mt_rand(1, 100);

        if ($roll <= 14) {
            return ['', $fatores['não conheço'] ?? $fatores['nao conheço'] ?? null];
        }

        if ($roll <= 22) {
            return ['', $fatores['conheço mas não lembro'] ?? $fatores['conheco mas nao lembro'] ?? null];
        }

        $candidato = $this->escolherCandidato($segmento);
        $texto = $this->aplicarVariacaoAleatoria($candidato);

        $fatorTitulos = [
            'qualidade no atendimento',
            'qualidade dos produtos',
            'preços praticados',
            'todos acima',
        ];
        $fatorKey = $fatorTitulos[array_rand($fatorTitulos)];
        $fatorId = $fatores[$fatorKey] ?? null;

        if ($texto === '') {
            return ['', $fatores['não conheço'] ?? null];
        }

        return [$texto, $fatorId];
    }

    /**
     * @return array{nome: string, peso: int, variacoes?: list<string>}
     */
    private function escolherCandidato(string $segmento): array
    {
        $lista = $this->demo['estabelecimentos'][$segmento] ?? $this->estabelecimentosGenericos($segmento);

        $pesoTotal = array_sum(array_column($lista, 'peso'));
        $sorteio = mt_rand(1, max(1, $pesoTotal));

        $acumulado = 0;

        foreach ($lista as $item) {
            $acumulado += $item['peso'];
            if ($sorteio <= $acumulado) {
                return $item;
            }
        }

        return $lista[0];
    }

    /**
     * @return list<array{nome: string, peso: int, variacoes?: list<string>}>
     */
    private function estabelecimentosGenericos(string $segmento): array
    {
        $sufixos = ['Bagé', 'Centro', 'Fronteira', 'Premium', 'Top'];

        return [
            ['nome' => "{$segmento} {$sufixos[0]}", 'peso' => 32],
            ['nome' => "{$segmento} {$sufixos[1]}", 'peso' => 24],
            ['nome' => "Melhor {$segmento}", 'peso' => 20, 'variacoes' => [mb_strtolower("Melhor {$segmento}")]],
            ['nome' => "{$segmento} {$sufixos[2]}", 'peso' => 14],
            ['nome' => "{$segmento} {$sufixos[3]}", 'peso' => 10],
        ];
    }

    /**
     * @param  array{nome: string, peso: int, variacoes?: list<string>}  $candidato
     */
    private function aplicarVariacaoAleatoria(array $candidato): string
    {
        $nome = $candidato['nome'];
        $variacoes = $candidato['variacoes'] ?? [];

        if (! empty($variacoes) && mt_rand(1, 100) <= 42) {
            return $variacoes[array_rand($variacoes)];
        }

        if (mt_rand(1, 100) <= 18) {
            return $this->gerarTypoLeve($nome);
        }

        if (mt_rand(1, 100) <= 10) {
            return mb_strtoupper($nome);
        }

        if (mt_rand(1, 100) <= 8) {
            return mb_strtolower($nome);
        }

        return $nome;
    }

    private function gerarTypoLeve(string $nome): string
    {
        $substituicoes = [
            'cultura' => 'cul',
            'icultura' => 'icul',
            'aria' => 'ária',
            'ão' => 'ao',
            'ç' => 'c',
            'á' => 'a',
            'é' => 'e',
            'í' => 'i',
            'ó' => 'o',
            'ú' => 'u',
        ];

        foreach ($substituicoes as $de => $para) {
            if (mt_rand(1, 100) <= 35 && str_contains(mb_strtolower($nome), $de)) {
                return str_ireplace($de, $para, $nome);
            }
        }

        if (str_contains($nome, ' ')) {
            return preg_replace('/\s+/', '  ', $nome, 1) ?? $nome;
        }

        return $nome;
    }

    /**
     * @return array{0: string, 1: null}
     */
    private function gerarRespostaDemografica(FormularioPergunta $pergunta): array
    {
        $texto = match ($pergunta->pergunta) {
            'Gênero' => self::GENEROS[array_rand(self::GENEROS)],
            'Idade' => self::IDADES[array_rand(self::IDADES)],
            'Profissão' => $this->demo['profissoes'][array_rand($this->demo['profissoes'])],
            default => $pergunta->opcoes->isNotEmpty()
                ? $pergunta->opcoes->random()->opcao
                : '',
        };

        if ($pergunta->pergunta === 'Profissão' && mt_rand(1, 100) <= 25) {
            $texto = '';
        }

        return [$texto, null];
    }
}
