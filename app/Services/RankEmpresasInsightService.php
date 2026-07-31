<?php

namespace App\Services;

use App\Models\FormularioFatorSatisfacao;
use App\Models\FormularioResposta as Resposta;
use App\Support\EmpresaRespostaClusterService;
use App\Support\InsightEmpresaAliasLoader;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class RankEmpresasInsightService
{
    public function __construct(
        private ClassificacaoRespostasService $classificacao,
    ) {}

    /**
     * @return array<int, array{grupo: \App\Models\FormularioPasso, perguntas: array<int, array{pergunta: \App\Models\FormularioPergunta, clusters: list<array>, opcoes_reconhecimento: list<array>, respostas_originais: \Illuminate\Support\Collection}>}>
     */
    public function dadosAgrupadosPorFormulario(int $formularioId): array
    {
        $dadosBrutos = $this->classificacao->dadosPorFormulario($formularioId);
        $clusterService = new EmpresaRespostaClusterService(InsightEmpresaAliasLoader::map());
        $fatoresReconhecimento = FormularioFatorSatisfacao::query()
            ->where('formulario_id', $formularioId)
            ->where('resposta_obrigatoria', false)
            ->orderBy('id')
            ->get(['id', 'titulo']);

        $dadosAgrupados = [];
        foreach ($dadosBrutos as $grupoId => $grupoData) {
            $perguntasFiltradas = [];
            foreach ($grupoData['perguntas'] as $perguntaId => $perguntaData) {
                if (! $perguntaData['pergunta']->usa_fatores_satisfacao) {
                    continue;
                }

                $opcoesReconhecimento = $this->contagensReconhecimento(
                    (int) $perguntaId,
                    $fatoresReconhecimento
                );
                $clusters = $clusterService->cluster($perguntaData['respostas']);
                $respostasOriginais = $this->enriquecerRespostasOriginais(
                    $perguntaData['respostas'],
                    $opcoesReconhecimento
                );

                $perguntasFiltradas[$perguntaId] = [
                    'pergunta' => $perguntaData['pergunta'],
                    'clusters' => $clusters,
                    'opcoes_reconhecimento' => $opcoesReconhecimento,
                    'respostas_originais' => $respostasOriginais,
                ];
            }
            if (count($perguntasFiltradas) === 0) {
                continue;
            }
            $dadosAgrupados[$grupoId] = [
                'grupo' => $grupoData['grupo'],
                'perguntas' => $perguntasFiltradas,
            ];
        }

        return $dadosAgrupados;
    }

    /**
     * Conta todas as respostas das opções sem nome de empresa (Não conheço / Conheço mas não lembro).
     *
     * @param  Collection<int, FormularioFatorSatisfacao>  $fatoresReconhecimento
     * @return list<array{canonical: string, total: int, variants: list<array{label: string, total: int, fator: ?string}>, fator_exibido: ?string, requer_validacao: bool, aviso_validacao: ?string, tipo: string}>
     */
    private function contagensReconhecimento(int $perguntaId, Collection $fatoresReconhecimento): array
    {
        if ($fatoresReconhecimento->isEmpty()) {
            return [];
        }

        $totaisPorFator = Resposta::query()
            ->select('fator_id', DB::raw('count(*) as total'))
            ->where('pergunta_id', $perguntaId)
            ->whereIn('fator_id', $fatoresReconhecimento->pluck('id'))
            ->groupBy('fator_id')
            ->pluck('total', 'fator_id');

        $opcoes = [];
        foreach ($fatoresReconhecimento as $fator) {
            $total = (int) ($totaisPorFator[$fator->id] ?? 0);
            $titulo = (string) $fator->titulo;

            $opcoes[] = [
                'canonical' => $titulo,
                'total' => $total,
                'variants' => [
                    ['label' => $titulo, 'total' => $total, 'fator' => $titulo],
                ],
                'fator_exibido' => $titulo,
                'requer_validacao' => false,
                'aviso_validacao' => null,
                'tipo' => 'reconhecimento',
            ];
        }

        return $opcoes;
    }

    /**
     * Substitui a linha vazia colapsada pelas opções de reconhecimento com totais reais.
     *
     * @param  Collection<int, object>  $respostas
     * @param  list<array{canonical: string, total: int, fator_exibido: ?string}>  $opcoesReconhecimento
     */
    private function enriquecerRespostasOriginais(Collection $respostas, array $opcoesReconhecimento): Collection
    {
        $semVazias = $respostas->filter(function ($r) {
            $t = trim((string) ($r->resposta ?? ''));

            return $t !== '' && strcasecmp($t, '(em branco)') !== 0;
        })->values();

        foreach ($opcoesReconhecimento as $opcao) {
            $semVazias->push((object) [
                'resposta' => $opcao['canonical'],
                'total' => $opcao['total'],
                'fator_mais_utilizado' => $opcao['fator_exibido'],
            ]);
        }

        return $semVazias->sortByDesc(fn ($r) => (int) $r->total)->values();
    }
}
