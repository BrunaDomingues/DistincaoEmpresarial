<?php

namespace App\Http\Controllers;

use App\Models\FormularioEnvio;
use Carbon\Carbon;

class FormularioEnvioController extends Controller
{
    public function show(FormularioEnvio $envio)
    {
        $envio->load([
            'formulario',
            'usuario:id,name,email',
            'respostas.pergunta.passo',
            'respostas.fator',
        ]);

        $respostasPorPasso = $envio->respostas
            ->sortBy(fn ($r) => [
                $r->pergunta->passo->ordem ?? 0,
                $r->pergunta->id ?? 0,
            ])
            ->groupBy(fn ($r) => $r->pergunta->passo->titulo ?? 'Sem passo');

        $dataEnvio = Carbon::parse($envio->fim_resposta ?? $envio->created_at)
            ->timezone('America/Sao_Paulo')
            ->format('d/m/Y H:i');

        return view('envios.show', compact('envio', 'respostasPorPasso', 'dataEnvio'));
    }
}
