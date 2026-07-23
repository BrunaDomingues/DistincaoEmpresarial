<?php

namespace App\Http\Controllers;

use App\Exports\RankEmpresasInsightExport;
use App\Models\Formulario;
use App\Services\RankEmpresasInsightService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class RankEmpresasInsightController extends Controller
{
    public function index()
    {
        $formularios = Formulario::orderBy('titulo')->get();

        return view('relatorios.rank_empresas_inicio', compact('formularios'));
    }

    public function analisar(Request $request, RankEmpresasInsightService $service)
    {
        $request->validate([
            'formulario_id' => 'required|integer|exists:formularios,id',
        ]);

        $formularioId = (int) $request->input('formulario_id');
        $formulario = Formulario::findOrFail($formularioId);
        $dadosAgrupados = $service->dadosAgrupadosPorFormulario($formularioId);

        return view('relatorios.rank_empresas_resultado', [
            'formulario' => $formulario,
            'dadosAgrupados' => $dadosAgrupados,
        ]);
    }

    public function exportar(Request $request)
    {
        $request->validate([
            'formulario_id' => 'required|integer|exists:formularios,id',
        ]);

        $formulario = Formulario::findOrFail((int) $request->input('formulario_id'));
        $nomeArquivo = 'ranking_empresas_'.Str::slug($formulario->titulo).'.xlsx';

        return Excel::download(
            new RankEmpresasInsightExport($formulario->id, $formulario->titulo),
            $nomeArquivo
        );
    }
}
