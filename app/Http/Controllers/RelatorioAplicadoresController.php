<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FormularioEnvio;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\RelatorioAplicadoresExport;
use App\Exports\RelatorioAplicadoresAcumuladoExport;

class RelatorioAplicadoresController extends Controller
{
    // Relatório por dia
    public function index(Request $request)
    {
        // Aplica filtro de data se fornecido
        $query = FormularioEnvio::selectRaw('DATE(created_at) as data, usuario_id, COUNT(*) as total')
            ->when($request->filled('data'), function ($q) use ($request) {
                $q->whereDate('created_at', $request->data);
            })
            ->where('invalido', 'false')
            ->groupBy('data', 'usuario_id')
            ->with('usuario');

        $envios = $query->paginate(20); // << ADICIONAR PAGINAÇÃO
        // $query->get();

        // Lista de datas disponíveis para filtro
        $datasDisponiveis = FormularioEnvio::selectRaw('DATE(created_at) as data')
            ->where('invalido', 'false')
            ->groupBy('data')
            ->orderBy('data', 'desc')
            ->pluck('data');

        return view('relatorios.aplicadores', compact('envios', 'datasDisponiveis'));
    }

    public function exportar(Request $request)
    {
        $data = $request->query('data');
        return Excel::download(new RelatorioAplicadoresExport($data), 'relatorio_aplicadores.xlsx');
    }

    //Relatório acumulado até uma data
    public function acumulado(Request $request)
    {
        $query = FormularioEnvio::selectRaw('usuario_id, COUNT(*) as total')
            ->when($request->filled('data'), function ($q) use ($request) {
                $q->whereDate('formulario_envios.created_at', '<=', $request->data);
            })
            ->where('invalido', 'false')
            ->groupBy('usuario_id')
            ->join('users', 'users.id', '=', 'formulario_envios.usuario_id') // join para ordenar pelo nome
            ->orderBy('users.name') // ordenar pelo nome do usuário
            ->with('usuario');
    
        $envios = $query->paginate(20);
    
        $datasDisponiveis = FormularioEnvio::selectRaw('DATE(created_at) as data')
            ->where('invalido', 'false')
            ->groupBy('data')
            ->orderBy('data', 'desc')
            ->pluck('data');
    
        return view('relatorios.aplicadores-acumulado', compact('envios', 'datasDisponiveis'));
    }   

    public function exportarAcumulado(Request $request)
    {
        $data = $request->query('data');
        return Excel::download(new RelatorioAplicadoresAcumuladoExport($data), 'relatorio_aplicadores_acumulado.xlsx');
    }
}