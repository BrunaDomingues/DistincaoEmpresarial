<?php

namespace App\Services;

use App\Models\FormularioPasso;
use App\Support\EmpresaRespostaClusterService;
use App\Support\InsightEmpresaAliasLoader;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class RelatorioMotivosSegmentoService
{
    /**
     * @return array{
     *     segmento: FormularioPasso,
     *     categoria: string,
     *     nome_segmento: string,
     *     graficos: list<array{posicao: int, canonical: string, total: int, motivos: list<array{label: string, total: int, percentual: float}>}>
     * }
     */
    public function dadosPorSegmento(int $passoId): array
    {
        $segmento = FormularioPasso::with('formulario')->findOrFail($passoId);
        [$categoria, $nomeSegmento] = $this->interpretarTitulo((string) $segmento->titulo);

        $perguntaIds = DB::table('formulario_perguntas')
            ->where('passo_id', $segmento->id)
            ->where('usa_fatores_satisfacao', true)
            ->pluck('id');

        $vazio = [
            'segmento' => $segmento,
            'categoria' => $categoria,
            'nome_segmento' => $nomeSegmento,
            'ranking' => [],
            'graficos' => [],
        ];

        if ($perguntaIds->isEmpty()) {
            return $vazio;
        }

        $agregadas = DB::table('formulario_respostas as fr')
            ->join('formulario_envios as fe', 'fe.id', '=', 'fr.formulario_envio_id')
            ->leftJoin('formulario_respostas_tratadas as frt', 'frt.resposta_id', '=', 'fr.id')
            ->whereIn('fr.pergunta_id', $perguntaIds)
            ->where('fe.invalido', 0)
            ->select([
                'fr.resposta',
                'frt.resposta_tratada',
                DB::raw('COUNT(*) as total'),
            ])
            ->groupBy('fr.resposta', 'frt.resposta_tratada')
            ->get()
            ->map(function ($linha) {
                $tratada = trim((string) ($linha->resposta_tratada ?? ''));
                $original = trim((string) ($linha->resposta ?? ''));

                return (object) [
                    'resposta' => $tratada !== '' ? $tratada : $original,
                    'total' => (int) $linha->total,
                    'fator_mais_utilizado' => null,
                ];
            })
            ->groupBy(fn ($linha) => mb_strtolower(trim((string) $linha->resposta)))
            ->map(function (Collection $grupo) {
                $primeiro = $grupo->sortByDesc('total')->first();
                $primeiro->total = $grupo->sum('total');

                return $primeiro;
            })
            ->values();

        $clusters = (new EmpresaRespostaClusterService(InsightEmpresaAliasLoader::map()))
            ->cluster($agregadas);

        $ranking = array_map(fn ($cluster) => [
            'canonical' => $cluster['canonical'],
            'total' => (int) $cluster['total'],
        ], $clusters);

        $linhasMotivos = $this->linhasMotivos($perguntaIds);

        $graficos = [];
        foreach ($clusters as $indice => $cluster) {
            $graficos[] = [
                'posicao' => $indice + 1,
                'canonical' => $cluster['canonical'],
                'total' => (int) $cluster['total'],
                'motivos' => $this->motivosDaEmpresa($linhasMotivos, $cluster),
            ];
        }

        return [
            'segmento' => $segmento,
            'categoria' => $categoria,
            'nome_segmento' => $nomeSegmento,
            'ranking' => $ranking,
            'graficos' => $graficos,
        ];
    }

    /**
     * @param  Collection<int, int|string>  $perguntaIds
     */
    private function linhasMotivos(Collection $perguntaIds): Collection
    {
        return DB::table('formulario_respostas as fr')
            ->join('formulario_envios as fe', 'fe.id', '=', 'fr.formulario_envio_id')
            ->leftJoin('formulario_respostas_tratadas as frt', 'frt.resposta_id', '=', 'fr.id')
            ->leftJoin('formularios_fator_satisfacao as ffs', 'ffs.id', '=', 'fr.fator_id')
            ->whereIn('fr.pergunta_id', $perguntaIds)
            ->where('fe.invalido', 0)
            ->whereNotNull('fr.fator_id')
            ->where(function ($query) {
                $query
                    ->where('ffs.resposta_obrigatoria', true)
                    ->orWhereRaw('LOWER(TRIM(ffs.titulo)) = ?', ['outros']);
            })
            ->select([
                'fr.resposta',
                'frt.resposta_tratada',
                'ffs.titulo as fator',
                'fr.input_fator',
                DB::raw('COUNT(*) as total'),
            ])
            ->groupBy('fr.resposta', 'frt.resposta_tratada', 'ffs.titulo', 'fr.input_fator')
            ->get();
    }

    /**
     * @param  array{variants: list<array{label: string}>}  $empresa
     * @return list<array{label: string, total: int, percentual: float}>
     */
    private function motivosDaEmpresa(Collection $linhas, array $empresa): array
    {
        $rotulos = collect($empresa['variants'] ?? [])
            ->pluck('label')
            ->map(fn ($label) => mb_strtolower(trim((string) $label)))
            ->filter()
            ->unique()
            ->values();

        if ($rotulos->isEmpty()) {
            return [];
        }

        $totais = [];
        foreach ($linhas as $linha) {
            $tratada = trim((string) ($linha->resposta_tratada ?? ''));
            $original = trim((string) ($linha->resposta ?? ''));
            $nome = mb_strtolower($tratada !== '' ? $tratada : $original);
            if (! $rotulos->contains($nome)) {
                continue;
            }

            $fator = trim((string) ($linha->fator ?? ''));
            if (mb_strtolower($fator) === 'outros') {
                $fator = 'Outro';
            }
            if ($fator === '') {
                continue;
            }

            $totais[$fator] = ($totais[$fator] ?? 0) + (int) $linha->total;
        }

        $ordem = [
            'Preços praticados',
            'Qualidade dos produtos',
            'Qualidade no atendimento',
            'Todos acima',
            'Outro',
        ];

        $soma = array_sum($totais);
        if ($soma === 0) {
            return [];
        }

        $motivos = [];
        foreach ($ordem as $label) {
            $total = (int) ($totais[$label] ?? 0);
            $motivos[] = [
                'label' => $label,
                'total' => $total,
                'percentual' => round(100 * $total / $soma, 2),
            ];
            unset($totais[$label]);
        }

        foreach ($totais as $label => $total) {
            $motivos[] = [
                'label' => $label,
                'total' => (int) $total,
                'percentual' => round(100 * $total / $soma, 2),
            ];
        }

        return $motivos;
    }

    /**
     * @return array{0: string, 1: string}
     */
    public function interpretarTitulo(string $titulo): array
    {
        if (preg_match('/^\d+\.\s*(.+?)\s*\(([^)]+)\)\s*$/u', $titulo, $match)) {
            return [trim($match[2]), trim($match[1])];
        }

        return ['Outros', trim($titulo)];
    }
}
