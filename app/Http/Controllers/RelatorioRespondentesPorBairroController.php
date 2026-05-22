<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\RespondentesPorBairroExport;

class RelatorioRespondentesPorBairroController extends Controller
{
    public function index(Request $request)
    {
        $query = DB::table('formulario_respostas_tratadas as frt')
            ->join('formulario_respostas as fr', 'frt.resposta_id', '=', 'fr.id')
            ->join('formulario_perguntas as fp', 'fr.pergunta_id', '=', 'fp.id')
            ->join('formulario_envios as fe', 'fr.formulario_envio_id', '=', 'fe.id') // Join com envios
            ->where('fp.pergunta', 'Bairro')
            ->where('fe.invalido', 0); // Apenas envios válidos
    
        if ($request->filled('data')) {
            $query->whereDate('fr.created_at', $request->data);
        }
    
        // Paginação com 15 itens por página
        $bairros = $query
            ->select('frt.resposta_tratada as bairro', DB::raw('COUNT(*) as total_respondentes'))
            ->groupBy('frt.resposta_tratada')
            ->orderBy('bairro')
            ->paginate(30)
            ->withQueryString(); // mantém os parâmetros da query string na paginação
    
        // Datas únicas dos envios válidos
        $datasDisponiveis = DB::table('formulario_respostas as fr')
            ->join('formulario_envios as fe', 'fr.formulario_envio_id', '=', 'fe.id')
            ->where('fe.invalido', 0)
            ->select(DB::raw('DATE(fr.created_at) as data'))
            ->distinct()
            ->orderBy('data', 'desc')
            ->pluck('data');
    
        return view('relatorios.respondentes_por_bairro', compact('bairros', 'datasDisponiveis'));
    }
    
    
    public function export()
    {
        return Excel::download(new RespondentesPorBairroExport, 'respondentes_por_bairro.xlsx');
    }
}
