<?php

namespace App\Http\Controllers;

use App\Models\FormularioPasso;
use App\Services\RelatorioMotivosSegmentoService;
use Illuminate\Http\Request;

class RelatorioMotivosSegmentoController extends Controller
{
    public function index(Request $request, RelatorioMotivosSegmentoService $service)
    {
        $passos = FormularioPasso::query()
            ->whereHas('perguntas', fn ($query) => $query->where('usa_fatores_satisfacao', true))
            ->with('formulario')
            ->orderBy('ordem')
            ->get()
            ->map(function (FormularioPasso $passo) use ($service) {
                [$categoria, $nome] = $service->interpretarTitulo((string) $passo->titulo);

                return [
                    'id' => $passo->id,
                    'categoria' => $categoria,
                    'nome' => $nome,
                    'titulo' => $passo->titulo,
                    'formulario' => $passo->formulario?->titulo,
                ];
            })
            ->values();

        $categorias = $passos->pluck('categoria')->unique()->sort()->values();

        $segmentoId = $request->integer('segmento_id') ?: null;
        $resultado = null;

        if ($segmentoId) {
            $resultado = $service->dadosPorSegmento($segmentoId);
        }

        return view('relatorios.motivos_segmento', [
            'passos' => $passos,
            'categorias' => $categorias,
            'segmentoId' => $segmentoId,
            'resultado' => $resultado,
        ]);
    }
}
