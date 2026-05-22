<?php

namespace App\Http\Controllers;

use App\Exports\RespondentesPorBairroExport;
use App\Support\RelatorioBairroQuery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class RelatorioRespondentesPorBairroController extends Controller
{
    public function index(Request $request)
    {
        $query = RelatorioBairroQuery::agrupadoPorBairro();

        if ($request->filled('data')) {
            $query->whereDate('fe.created_at', $request->data);
        }

        $bairros = $query
            ->orderBy('bairro')
            ->paginate(30)
            ->withQueryString();

        $datasDisponiveis = DB::table('formulario_envios as fe')
            ->where('fe.invalido', 0)
            ->whereNotNull('fe.bairro')
            ->whereRaw("TRIM(fe.bairro) <> ''")
            ->select(DB::raw('DATE(fe.created_at) as data'))
            ->distinct()
            ->orderBy('data', 'desc')
            ->pluck('data');

        return view('relatorios.respondentes_por_bairro', compact('bairros', 'datasDisponiveis'));
    }

    public function export(Request $request)
    {
        return Excel::download(
            new RespondentesPorBairroExport($request->input('data')),
            'respondentes_por_bairro.xlsx'
        );
    }
}
