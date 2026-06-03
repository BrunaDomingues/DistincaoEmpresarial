<?php

namespace App\Http\Controllers;

use App\Exports\RelatorioEnviosUsuariosExport;
use App\Models\Formulario;
use App\Models\FormularioEnvio;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class RelatorioEnviosUsuariosController extends Controller
{
    public function index(Request $request)
    {
        $timezone = 'America/Sao_Paulo';

        $query = FormularioEnvio::query()
            ->with(['usuario:id,name,email', 'formulario:id,titulo'])
            ->where('invalido', false)
            ->when($request->filled('formulario_id'), function ($q) use ($request) {
                $q->where('formulario_id', (int) $request->input('formulario_id'));
            })
            ->when($request->filled('data'), function ($q) use ($request) {
                $q->whereRaw('DATE(COALESCE(fim_resposta, created_at)) = ?', [$request->input('data')]);
            })
            ->orderByRaw('COALESCE(fim_resposta, created_at) DESC');

        $envios = $query->get();

        $enviosPorDia = $envios
            ->groupBy(function (FormularioEnvio $envio) use ($timezone) {
                $momento = $envio->fim_resposta ?? $envio->created_at;

                return Carbon::parse($momento)->timezone($timezone)->format('Y-m-d');
            })
            ->sortKeysDesc();

        $enviosPorDia = $enviosPorDia->map(function ($itens, string $diaChave) use ($timezone) {
            $data = Carbon::parse($diaChave)->timezone($timezone);

            return [
                'data' => $data,
                'label' => $data->locale('pt_BR')->translatedFormat('l, d/m/Y'),
                'itens' => $itens->map(function (FormularioEnvio $envio) use ($timezone) {
                    $momento = Carbon::parse($envio->fim_resposta ?? $envio->created_at)->timezone($timezone);

                    return [
                        'envio' => $envio,
                        'horario' => $momento->format('H:i:s'),
                        'horario_curto' => $momento->format('H:i'),
                    ];
                })->values(),
            ];
        });

        $formularios = Formulario::orderBy('titulo')->get(['id', 'titulo']);

        $datasDisponiveis = FormularioEnvio::query()
            ->where('invalido', false)
            ->selectRaw('DATE(COALESCE(fim_resposta, created_at)) as data')
            ->groupBy('data')
            ->orderByDesc('data')
            ->pluck('data');

        $totalEnvios = $envios->count();

        return view('relatorios.envios_usuarios', compact(
            'enviosPorDia',
            'formularios',
            'datasDisponiveis',
            'totalEnvios'
        ));
    }

    public function exportar(Request $request)
    {
        $formularioId = $request->filled('formulario_id')
            ? (int) $request->input('formulario_id')
            : null;

        return Excel::download(
            new RelatorioEnviosUsuariosExport($formularioId, $request->input('data')),
            'envios_por_usuario.xlsx'
        );
    }
}
