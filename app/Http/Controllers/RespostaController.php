<?php

namespace App\Http\Controllers;

use App\Models\Formulario;
use App\Models\FormularioResposta;
use App\Models\FormularioRespostaTratada;
use App\Models\FormularioFatorSatisfacao;
use App\Models\FormularioEnvio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class RespostaController extends Controller
{
    public function store(Request $request, Formulario $formulario)
    {
        if (! $formulario->estaDisponivel()) {
            return response()->json([
                'success' => false,
                'message' => $formulario->mensagemIndisponibilidade(),
            ], 403);
        }

        // Validação inicial básica
        $request->validate([
            'respostas' => 'required|array',
            'fatores' => 'nullable|array',
            'input_fatores' => 'nullable|array',  // Novo campo para textos extras
        ]);

        // Validação dinâmica para fatores obrigatórios
        $regrasFatores = [];

        foreach ($formulario->passos as $passo) {
            foreach ($passo->perguntas as $pergunta) {
                if ($pergunta->usa_fatores_satisfacao && $pergunta->resposta_obrigatoria) {
                    $regrasFatores["fatores.{$pergunta->id}"] = 'required';
                }
            }
        }

        // Valida regras específicas de fatores
        $request->validate($regrasFatores);

        $ip = $request->header('CF-Connecting-IP', $request->ip());
        $geoInfo = $this->resolveGeoInfo($ip);

        $duracao = (int) $request->input('duracao_em_segundos', 0);
        $inicio = Carbon::parse($request->input('inicio_resposta'))->setTimezone('America/Sao_Paulo');
        $fim = Carbon::parse($request->input('fim_resposta'))->setTimezone('America/Sao_Paulo');

        $formularioEnvio = FormularioEnvio::create([
            'formulario_id' => $formulario->id,
            'usuario_id' => Auth::id(),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'geo_info' => $geoInfo,
            'latitude' => $request->input('latitude'),
            'longitude' => $request->input('longitude'),
            'rua' => $request->input('rua'),
            'bairro' => $request->input('bairro'),
            'cidade' => $request->input('cidade'),
            'estado' => $request->input('estado'),
            'inicio_resposta' => $inicio,
            'fim_resposta' => $fim,
            'duracao_em_segundos' => $duracao,
        ]);

        foreach ($request->respostas as $pergunta_id => $resposta) {
            $resposta_original = is_array($resposta) ? implode(', ', $resposta) : $resposta;

            // Pega o texto extra enviado pelo input extra, se existir
            $fatorId = $request->fatores[$pergunta_id] ?? null;
            
            $inputFatorTexto = "";
            if ($fatorId && isset($request->fator_extra[$pergunta_id][$fatorId])) {
                $inputFatorTexto = $request->fator_extra[$pergunta_id][$fatorId] ?? '';
            }

            $respostaSalva = FormularioResposta::create([
                'formulario_envio_id' => $formularioEnvio->id,
                'pergunta_id' => $pergunta_id,
                'usuario_id' => Auth::id(),
                'resposta' => $resposta_original,
                'fator_id' => $fatorId,
                'input_fator' => $inputFatorTexto,
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);
        
            FormularioRespostaTratada::create([
                'resposta_id' => $respostaSalva->id,
                'resposta_tratada' => $resposta_original,
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);
        }
        

        return response()->json([
            'success' => true,
            'message' => 'Respostas salvas com sucesso!',
        ]);
    }

    public function create(Formulario $formulario)
    {
        if (! $formulario->estaDisponivel()) {
            return redirect()
                ->route('responder-formularios.index')
                ->with('error', $formulario->mensagemIndisponibilidade());
        }

        return view('formularios.responder.responder', [
            'formulario' => $formulario,
            'fatoresSatisfacao' => FormularioFatorSatisfacao::where('formulario_id', $formulario->id)->get(),
        ]);
        
    }

    private function resolveGeoInfo(?string $ip): ?array
    {
        if (! $ip || ! $this->isPublicIp($ip)) {
            return null;
        }

        try {
            $response = Http::timeout(5)->get("https://ipapi.co/{$ip}/json/");

            return $response->ok() ? $response->json() : null;
        } catch (\Throwable) {
            return null;
        }
    }

    private function isPublicIp(string $ip): bool
    {
        return filter_var(
            $ip,
            FILTER_VALIDATE_IP,
            FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE
        ) !== false;
    }

}
