<?php

namespace App\Http\Controllers;

use App\Models\Formulario;
use App\Services\ClassificacaoRespostasService;
use App\Support\EmpresaRespostaClusterService;
use Illuminate\Http\Request;

class RankEmpresasInsightController extends Controller
{
    public function index()
    {
        $formularios = Formulario::orderBy('titulo')->get();

        return view('relatorios.rank_empresas_inicio', compact('formularios'));
    }

    public function analisar(Request $request, ClassificacaoRespostasService $classificacao)
    {
        $request->validate([
            'formulario_id' => 'required|integer|exists:formularios,id',
        ]);

        $formularioId = (int) $request->input('formulario_id');
        $formulario = Formulario::findOrFail($formularioId);

        $dadosBrutos = $classificacao->dadosPorFormulario($formularioId);
        $clusterService = new EmpresaRespostaClusterService;

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

        return view('relatorios.rank_empresas_resultado', [
            'formulario' => $formulario,
            'dadosAgrupados' => $dadosAgrupados,
        ]);
    }
}
