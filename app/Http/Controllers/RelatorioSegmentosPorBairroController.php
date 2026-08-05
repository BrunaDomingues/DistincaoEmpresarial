<?php

namespace App\Http\Controllers;

use App\Exports\SegmentosPorBairroExport;
use App\Models\Formulario;
use App\Models\FormularioPasso;
use App\Services\RelatorioSegmentoPorBairroService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class RelatorioSegmentosPorBairroController extends Controller
{
    public function index()
    {
        $formularios = Formulario::query()
            ->whereHas('passos.perguntas', fn ($query) => $query->where('usa_fatores_satisfacao', true))
            ->with(['passos' => function ($query) {
                $query
                    ->whereHas('perguntas', fn ($perguntas) => $perguntas->where('usa_fatores_satisfacao', true))
                    ->orderBy('ordem');
            }])
            ->orderBy('titulo')
            ->get();

        $cidades = DB::table('formulario_envios')
            ->where('invalido', 0)
            ->whereNotNull('cidade')
            ->whereRaw("TRIM(cidade) <> ''")
            ->selectRaw('TRIM(cidade) as cidade')
            ->distinct()
            ->orderBy('cidade')
            ->pluck('cidade');

        return view('relatorios.segmentos_por_bairro_inicio', compact('formularios', 'cidades'));
    }

    public function analisar(Request $request, RelatorioSegmentoPorBairroService $service)
    {
        $dadosValidados = $request->validate([
            'segmento_id' => ['required', 'integer', 'exists:formulario_passos,id'],
            'cidade' => ['nullable', 'string', 'max:255'],
        ]);

        $cidade = $dadosValidados['cidade'] ?? null;
        $resultado = $service->dadosPorSegmento((int) $dadosValidados['segmento_id'], $cidade);

        return view('relatorios.segmentos_por_bairro_resultado', [
            'formulario' => $resultado['segmento']->formulario,
            'segmento' => $resultado['segmento'],
            'bairros' => $resultado['bairros'],
            'perguntasEncontradas' => $resultado['perguntas_encontradas'],
            'cidadeSelecionada' => $cidade,
        ]);
    }

    public function exportar(Request $request)
    {
        $dadosValidados = $request->validate([
            'segmento_id' => ['required', 'integer', 'exists:formulario_passos,id'],
            'cidade' => ['nullable', 'string', 'max:255'],
        ]);

        $segmento = FormularioPasso::with('formulario')->findOrFail((int) $dadosValidados['segmento_id']);
        $cidade = $dadosValidados['cidade'] ?? null;

        return Excel::download(
            new SegmentosPorBairroExport($segmento->id, $cidade),
            'resultados_por_bairro_'.Str::slug($segmento->titulo).'.xlsx'
        );
    }
}
