<?php

namespace App\Services;

use App\Models\FormularioPasso;
use App\Support\EmpresaRespostaClusterService;
use App\Support\InsightEmpresaAliasLoader;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class RelatorioSegmentoPorBairroService
{
    /**
     * @return array{bairros: list<array{cidade: string, bairro: string, total: int, clusters: list<array>}>, perguntas_encontradas: int, segmento: FormularioPasso}
     */
    public function dadosPorSegmento(int $passoId, ?string $cidade = null): array
    {
        $segmento = FormularioPasso::with('formulario')->findOrFail($passoId);
        $perguntaIds = DB::table('formulario_perguntas')
            ->where('passo_id', $segmento->id)
            ->where('usa_fatores_satisfacao', true)
            ->pluck('id');

        if ($perguntaIds->isEmpty()) {
            return [
                'bairros' => [],
                'perguntas_encontradas' => 0,
                'segmento' => $segmento,
            ];
        }

        $query = DB::table('formulario_respostas as fr')
            ->join('formulario_envios as fe', 'fe.id', '=', 'fr.formulario_envio_id')
            ->join('formulario_respostas_tratadas as frt', 'frt.resposta_id', '=', 'fr.id')
            ->leftJoin('formularios_fator_satisfacao as ffs', 'ffs.id', '=', 'fr.fator_id')
            ->whereIn('fr.pergunta_id', $perguntaIds)
            ->where('fe.formulario_id', $segmento->formulario_id)
            ->where('fe.invalido', 0)
            ->whereNotNull('fe.bairro')
            ->whereRaw("TRIM(fe.bairro) <> ''")
            ->whereNotNull('frt.resposta_tratada')
            ->whereRaw("TRIM(frt.resposta_tratada) <> ''")
            ->select([
                DB::raw("COALESCE(NULLIF(TRIM(fe.cidade), ''), 'Cidade não informada') as cidade"),
                DB::raw('TRIM(fe.bairro) as bairro'),
                'frt.resposta_tratada as resposta',
                'ffs.titulo as fator',
                'fr.input_fator',
                DB::raw('COUNT(*) as total'),
            ])
            ->groupBy(
                'cidade',
                DB::raw('TRIM(fe.bairro)'),
                'frt.resposta_tratada',
                'ffs.titulo',
                'fr.input_fator'
            );

        if ($cidade !== null && trim($cidade) !== '') {
            $query->whereRaw('LOWER(TRIM(fe.cidade)) = ?', [mb_strtolower(trim($cidade))]);
        }

        $linhas = $query->get();

        $clusterService = new EmpresaRespostaClusterService(InsightEmpresaAliasLoader::map());

        $bairros = $linhas
            ->groupBy(fn ($linha) => mb_strtolower(trim((string) $linha->cidade)).'|'.mb_strtolower(trim((string) $linha->bairro)))
            ->map(function (Collection $linhasDoBairro) use ($clusterService): array {
                $cidade = trim((string) $linhasDoBairro->first()->cidade);
                $bairro = trim((string) $linhasDoBairro->first()->bairro);
                $respostas = $linhasDoBairro
                    ->groupBy(fn ($linha) => trim((string) $linha->resposta))
                    ->map(function (Collection $ocorrencias) {
                        $fatores = $ocorrencias
                            ->groupBy(function ($linha) {
                                $fator = trim((string) ($linha->fator ?? ''));

                                if (mb_strtolower($fator) === 'outros') {
                                    return trim((string) ($linha->input_fator ?: 'Outros'));
                                }

                                return $fator;
                            })
                            ->map(fn (Collection $itens) => $itens->sum(fn ($item) => (int) $item->total))
                            ->sortDesc();

                        return (object) [
                            'resposta' => trim((string) $ocorrencias->first()->resposta),
                            'total' => $ocorrencias->sum(fn ($item) => (int) $item->total),
                            'fator_mais_utilizado' => $fatores->keys()->first() ?: null,
                        ];
                    })
                    ->values();

                $clusters = $clusterService->cluster($respostas);

                return [
                    'cidade' => $cidade,
                    'bairro' => $bairro,
                    'total' => array_sum(array_column($clusters, 'total')),
                    'clusters' => $clusters,
                ];
            })
            ->sortBy(fn (array $bairro) => mb_strtolower($bairro['cidade'].'|'.$bairro['bairro']))
            ->values()
            ->all();

        return [
            'bairros' => $bairros,
            'perguntas_encontradas' => $perguntaIds->count(),
            'segmento' => $segmento,
        ];
    }
}
