<?php

namespace App\Services;

use App\Models\FormularioFatorSatisfacao as FatorSatisfacao;
use App\Models\FormularioPasso as GrupoPergunta;
use App\Models\FormularioResposta as Resposta;
use Illuminate\Support\Facades\DB;

class ClassificacaoRespostasService
{
    /**
     * Mesma estrutura que RelatorioClassificacaoController::classificacaoFiltrar.
     *
     * @return array<int, array{grupo: \App\Models\FormularioPasso, perguntas: array<int, array{pergunta: \App\Models\FormularioPergunta, respostas: \Illuminate\Support\Collection}>}>
     */
    public function dadosPorFormulario(int $formularioId): array
    {
        $grupos = GrupoPergunta::with('perguntas')->where('formulario_id', $formularioId)->get();
        $dados = [];

        foreach ($grupos as $grupo) {
            foreach ($grupo->perguntas as $pergunta) {
                $respostasAgrupadas = Resposta::select(
                    'formulario_respostas_tratadas.resposta_tratada as resposta',
                    DB::raw('count(*) as total')
                )
                    ->leftJoin(
                        'formulario_respostas_tratadas',
                        'formulario_respostas_tratadas.resposta_id',
                        '=',
                        'formulario_respostas.id'
                    )
                    ->where('formulario_respostas.pergunta_id', $pergunta->id)
                    ->groupBy('formulario_respostas_tratadas.resposta_tratada')
                    ->orderBy('formulario_respostas_tratadas.resposta_tratada', 'asc')
                    ->get();

                foreach ($respostasAgrupadas as $respostaAgrupada) {
                    $fatorMaisUtilizado = Resposta::select('fator_id', DB::raw('count(*) as total_fator'))
                        ->where('pergunta_id', $pergunta->id)
                        ->where('resposta', $respostaAgrupada->resposta)
                        ->groupBy('fator_id')
                        ->orderByDesc('total_fator')
                        ->first();

                    $fator = null;

                    if ($fatorMaisUtilizado) {
                        $fatorObj = FatorSatisfacao::find($fatorMaisUtilizado->fator_id);

                        if ($fatorObj && strtolower(trim($fatorObj->titulo)) === 'outros') {
                            $inputFatorMaisUsado = Resposta::where('pergunta_id', $pergunta->id)
                                ->where('resposta', $respostaAgrupada->resposta)
                                ->where('fator_id', $fatorMaisUtilizado->fator_id)
                                ->select('input_fator', DB::raw('count(*) as total'))
                                ->groupBy('input_fator')
                                ->orderByDesc('total')
                                ->first();

                            $fator = $inputFatorMaisUsado->input_fator ?? 'Outros';
                        } else {
                            $fator = $fatorObj?->titulo;
                        }
                    }

                    $respostaAgrupada->fator_mais_utilizado = $fator;
                }

                $dados[$grupo->id]['grupo'] = $grupo;
                $dados[$grupo->id]['perguntas'][$pergunta->id] = [
                    'pergunta' => $pergunta,
                    'respostas' => $respostasAgrupadas,
                ];
            }
        }

        return $dados;
    }
}
