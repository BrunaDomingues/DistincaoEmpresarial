<?php

namespace Database\Seeders;

use App\Models\Formulario;
use App\Models\FormularioFatorSatisfacao;
use App\Models\FormularioOpcao;
use App\Models\FormularioPasso;
use App\Models\FormularioPergunta;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DistincaoEmpresarialSeeder extends Seeder
{
    /**
     * Apaga formulários, respostas e envios antes de recriar.
     */
    public static bool $limparAntes = true;

    /**
     * Criar só alguns questionários (ex.: [1, 5]). null = todos (1 a 5).
     *
     * @var array<int>|null
     */
    public static ?array $somenteQuestionarios = null;

    private const FATORES = [
        ['titulo' => 'Não conheço', 'resposta_obrigatoria' => false],
        ['titulo' => 'Conheço mas não lembro', 'resposta_obrigatoria' => false],
        ['titulo' => 'Qualidade no atendimento', 'resposta_obrigatoria' => true],
        ['titulo' => 'Qualidade dos produtos', 'resposta_obrigatoria' => true],
        ['titulo' => 'Preços praticados', 'resposta_obrigatoria' => true],
        ['titulo' => 'Todos acima', 'resposta_obrigatoria' => true],
    ];

    private const DESCRICAO_INTRO = 'Estamos realizando uma pesquisa que busca identificar quais são as empresas e profissionais mais lembrados da cidade de Bagé-RS, por suas qualidades. As informações são para agraciá-los com o prêmio "Distinção Empresarial" concedido anualmente pela Associação Comercial e Industrial de Bagé – ACIBA. Não é necessário se identificar.';

    public function run(): void
    {
        $questionarios = require database_path('data/distincao_questionarios.php');
        $numeros = self::$somenteQuestionarios ?? array_keys($questionarios);

        DB::transaction(function () use ($questionarios, $numeros) {
            if (self::$limparAntes) {
                $this->limparDadosFormularios();
                $this->command?->warn('Dados de formulários, respostas e envios removidos.');
            }

            $numeroGlobal = 0;

            foreach ($numeros as $num) {
                if (! isset($questionarios[$num])) {
                    $this->command?->error("Questionário inválido: {$num}");

                    continue;
                }

                $config = $questionarios[$num];
                $numeroGlobal = $this->criarQuestionario($num, $config, $numeroGlobal);
            }

            $this->command?->info("Concluído: {$numeroGlobal} segmentos em ".count($numeros).' questionário(s).');
        });
    }

    private function limparDadosFormularios(): void
    {
        Schema::disableForeignKeyConstraints();

        DB::table('formulario_respostas_tratadas')->delete();
        DB::table('formulario_respostas')->delete();
        DB::table('formulario_envios')->delete();
        DB::table('formulario_opcoes')->delete();
        DB::table('formulario_perguntas')->delete();
        DB::table('formulario_passos')->delete();
        DB::table('formularios_fator_satisfacao')->delete();
        DB::table('formularios')->delete();

        Schema::enableForeignKeyConstraints();
    }

    private function criarQuestionario(int $numero, array $config, int $numeroGlobal): int
    {
        $formulario = Formulario::create([
            'titulo' => $config['titulo'],
            'descricao' => self::DESCRICAO_INTRO,
            'data_inicio' => null,
            'data_fim' => null,
        ]);

        $this->criarFatores($formulario->id);

        $ordem = 1;
        $this->criarPassoDemografico($formulario->id, $ordem++);

        $inicioGlobal = $numeroGlobal + 1;

        foreach ($config['segmentos'] as $segmento) {
            $numeroGlobal++;
            $this->criarPassoSegmento($formulario->id, $segmento, $ordem++, $numeroGlobal);
        }

        $total = count($config['segmentos']);
        $this->command?->info(sprintf(
            'Questionário %d: "%s" — %d segmentos (nº %d a %d).',
            $numero,
            $config['titulo'],
            $total,
            $inicioGlobal,
            $numeroGlobal
        ));

        return $numeroGlobal;
    }

    private function criarFatores(int $formularioId): void
    {
        foreach (self::FATORES as $fator) {
            FormularioFatorSatisfacao::create([
                'formulario_id' => $formularioId,
                'titulo' => $fator['titulo'],
                'resposta_obrigatoria' => $fator['resposta_obrigatoria'],
                'usa_input_extra' => false,
            ]);
        }
    }

    private function criarPassoDemografico(int $formularioId, int $ordem): void
    {
        $passo = FormularioPasso::create([
            'formulario_id' => $formularioId,
            'titulo' => 'Dados gerais',
            'ordem' => $ordem,
        ]);

        $perguntaGenero = FormularioPergunta::create([
            'passo_id' => $passo->id,
            'tipo' => 'radio',
            'pergunta' => 'Gênero',
            'obrigatorio' => true,
            'usa_fatores_satisfacao' => false,
        ]);
        foreach (['Masculino', 'Feminino', 'Outro'] as $opcao) {
            FormularioOpcao::create(['pergunta_id' => $perguntaGenero->id, 'opcao' => $opcao]);
        }

        $perguntaIdade = FormularioPergunta::create([
            'passo_id' => $passo->id,
            'tipo' => 'radio',
            'pergunta' => 'Idade',
            'obrigatorio' => true,
            'usa_fatores_satisfacao' => false,
        ]);
        foreach (
            [
                '18 a 28 anos',
                '29 a 39 anos',
                '40 a 50 anos',
                '51 a 61 anos',
                'Mais de 61 anos',
            ] as $opcao
        ) {
            FormularioOpcao::create(['pergunta_id' => $perguntaIdade->id, 'opcao' => $opcao]);
        }

        FormularioPergunta::create([
            'passo_id' => $passo->id,
            'tipo' => 'texto',
            'pergunta' => 'Profissão',
            'obrigatorio' => false,
            'usa_fatores_satisfacao' => false,
        ]);
    }

    private function criarPassoSegmento(int $formularioId, array $segmento, int $ordem, int $numeroGlobal): void
    {
        $categoriaLabel = match ($segmento['categoria']) {
            'profissional_liberal' => 'Profissional Liberal',
            'industria' => 'Indústria',
            'prestadores_servicos' => 'Prestadores de Serviços',
            'comercio' => 'Comércio',
            default => ucfirst($segmento['categoria']),
        };

        $passo = FormularioPasso::create([
            'formulario_id' => $formularioId,
            'titulo' => sprintf('%d. %s (%s)', $numeroGlobal, $segmento['nome'], $categoriaLabel),
            'ordem' => $ordem,
        ]);

        FormularioPergunta::create([
            'passo_id' => $passo->id,
            'tipo' => 'texto',
            'pergunta' => sprintf(
                '%d. %s — qual empresa ou profissional você mais lembra?',
                $numeroGlobal,
                $segmento['nome']
            ),
            'obrigatorio' => false,
            'usa_fatores_satisfacao' => true,
        ]);
    }
}
