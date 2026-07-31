<?php

namespace App\Http\Controllers;

use App\Exports\RankReconhecimentoPesquisadoresExport;
use App\Models\Formulario;
use App\Services\RankReconhecimentoPesquisadoresService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class RankReconhecimentoPesquisadoresController extends Controller
{
    public function index()
    {
        $formularios = Formulario::orderBy('titulo')->get();

        return view('relatorios.rank_reconhecimento_pesquisadores_inicio', compact('formularios'));
    }

    public function analisar(Request $request, RankReconhecimentoPesquisadoresService $service)
    {
        $request->validate([
            'formulario_id' => 'required|integer|exists:formularios,id',
        ]);

        $formulario = Formulario::findOrFail((int) $request->input('formulario_id'));
        $dados = $service->dadosPorFormulario($formulario->id);

        return view('relatorios.rank_reconhecimento_pesquisadores_resultado', [
            'formulario' => $formulario,
            'fatores' => $dados['fatores'],
            'ranking' => $dados['ranking'],
            'porPergunta' => $dados['por_pergunta'],
        ]);
    }

    public function exportar(Request $request)
    {
        $request->validate([
            'formulario_id' => 'required|integer|exists:formularios,id',
        ]);

        $formulario = Formulario::findOrFail((int) $request->input('formulario_id'));
        $nomeArquivo = 'ranking_reconhecimento_pesquisadores_'.Str::slug($formulario->titulo).'.xlsx';

        return Excel::download(
            new RankReconhecimentoPesquisadoresExport($formulario->id, $formulario->titulo),
            $nomeArquivo
        );
    }
}
