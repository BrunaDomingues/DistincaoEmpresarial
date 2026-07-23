<?php

namespace App\Services;

use App\Support\EmpresaRespostaClusterService;
use App\Support\InsightEmpresaAliasLoader;

class RankEmpresasInsightService
{
    public function __construct(
        private ClassificacaoRespostasService $classificacao,
    ) {}

    /**
     * @return array<int, array{grupo: \App\Models\FormularioPasso, perguntas: array<int, array{pergunta: \App\Models\FormularioPergunta, clusters: list<array>, respostas_originais: \Illuminate\Support\Collection}>}>
     */
    public function dadosAgrupadosPorFormulario(int $formularioId): array
    {
        $dadosBrutos = $this->classificacao->dadosPorFormulario($formularioId);
        $clusterService = new EmpresaRespostaClusterService(InsightEmpresaAliasLoader::map());

        $dadosAgrupados = [];
        foreach ($dadosBrutos as $grupoId => $grupoData) {
            $perguntasFiltradas = [];
            foreach ($grupoData['perguntas'] as $perguntaId => $perguntaData) {
                if (! $perguntaData['pergunta']->usa_fatores_satisfacao) {
                    continue;
                }
                $clusters = $clusterService->cluster($perguntaData['respostas']);
                $perguntasFiltradas[$perguntaId] = [
                    'pergunta' => $perguntaData['pergunta'],
                    'clusters' => $clusters,
                    'respostas_originais' => $perguntaData['respostas'],
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
}
