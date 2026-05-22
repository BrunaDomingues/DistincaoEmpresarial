<?php

namespace App\Http\Controllers;

use App\Models\Formulario;
use App\Models\FormularioPasso;
use App\Models\FormularioPergunta;
use App\Models\FormularioResposta;
use App\Models\FormularioRespostaTratada;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RespostaTratadaController extends Controller
{
    public function index(Request $request)
    {
        $respostas = FormularioResposta::select('formulario_respostas.*')
                    ->join('formulario_perguntas', 'formulario_perguntas.id', '=', 'formulario_respostas.pergunta_id')
                    ->join('formulario_passos', 'formulario_passos.id', '=', 'formulario_perguntas.passo_id')
                    ->join('formularios', 'formularios.id', '=', 'formulario_passos.formulario_id')
                    ->with([
                        'pergunta.passo.formulario',
                        'respostaTratada',
                    ])
                    ->when($request->formulario_id, function ($query) use ($request) {
                        $query->where('formularios.id', $request->formulario_id);
                    })
                    ->when($request->filled('pergunta'), function ($query) use ($request) {
                        $query->where('formulario_perguntas.pergunta', 'like', '%' . $request->pergunta . '%');
                    })                   
                    ->when($request->filled('conferida'), function ($query) use ($request) {
                        $query->whereHas('respostaTratada', function ($q) use ($request) {
                            $q->where('conferida', $request->conferida);
                        });
                    })
                    ->orderBy('formularios.id', 'asc')
                    ->orderBy('formulario_passos.id', 'desc')
                    ->orderBy('formulario_perguntas.id', 'desc')
                    ->paginate(20);
    
        $formularios = Formulario::all();
    
        return view('formularios.tratamento-respostas', compact('respostas', 'formularios'));
    }
    
    public function update(Request $request, FormularioRespostaTratada $respostaTratada)
    {
        $request->validate([
            'resposta_tratada' => 'required|string',
            'conferida' => 'nullable|boolean',
        ]);

        $respostaTratada->update([
            'resposta_tratada' => $request->resposta_tratada,
            // opcional: marcar como conferida ao editar
            'conferida' => $request->boolean('conferida'),
            'updated_by' => auth()->id(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Resposta tratada atualizada com sucesso.'
        ]);
    }

    public function dados($id)
    {
        $respostaTratada = FormularioRespostaTratada::with([
            'resposta.pergunta.opcoes',
            'resposta.pergunta.passo.formulario'
        ])->findOrFail($id);

        $pergunta = $respostaTratada->resposta->pergunta;

        return response()->json([
            'pergunta_id' => $pergunta->id,
            'pergunta' => $pergunta->pergunta,
            'tipo' => $pergunta->tipo,
            'opcoes' => $pergunta->opcoes->pluck('opcao'),
            'resposta_id' => $respostaTratada->resposta_id,
            'resposta_tratada' => $respostaTratada->resposta_tratada,
            'conferida' => $respostaTratada->conferida,
            'grupo' => $pergunta->passo->titulo ?? '',
            'formulario' => $pergunta->passo->formulario->titulo ?? '',
        ]);
    }

}

