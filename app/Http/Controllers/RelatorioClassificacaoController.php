<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
//use Maatwebsite\Excel\Facades\Excel;
//use App\Exports\RelatorioAplicadoresExport;
//use App\Exports\RelatorioAplicadoresAcumuladoExport;
use App\Models\Formulario;
use App\Services\ClassificacaoRespostasService;

class RelatorioClassificacaoController extends Controller
{
    public function classificacao()
    {
        $formularios = Formulario::all();
        return view('relatorios.classificacao', compact('formularios'));
    }

    public function classificacaoFiltrar(Request $request, ClassificacaoRespostasService $classificacaoRespostas)
    {
        $formularioId = (int) $request->input('formulario_id');

        $dados = $classificacaoRespostas->dadosPorFormulario($formularioId);

        return view('relatorios.classificacao_resultado', compact('dados'));
    }
}