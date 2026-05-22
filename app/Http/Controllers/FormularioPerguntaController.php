<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FormularioPergunta;
use Illuminate\Support\Facades\Auth;

class FormularioPerguntaController extends Controller
{
    public function index(Request $request)
    {
        // Busca as perguntas associadas ao passo_id
        $perguntas = FormularioPergunta::with(['opcoes'])->where('passo_id', $request->passo_id)->get();
    
        return response()->json($perguntas);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'passo_id' => 'required|exists:formulario_passos,id',
            'pergunta' => 'required|string|max:255',
            'tipo' => 'required|string'
        ]);

        $validated['obrigatorio'] = $request->has('obrigatorio');
        $validated['usa_fatores_satisfacao'] = $request->has('usa_fatores');

        $validated['created_by'] = Auth::id();
        $validated['updated_by'] = Auth::id();
        $pergunta = FormularioPergunta::create($validated);

        $pergunta = FormularioPergunta::with('opcoes')->find($pergunta->id);

        return response()->json([
            'success' => true,
            'message' => 'Pergunta adicionada com sucesso.',
            'data' => $pergunta
        ]);
    }

    public function destroy($id)
    {
        $pergunta = FormularioPergunta::findOrFail($id);
        $pergunta->delete();

        return back()->with('success', 'Pergunta removida com sucesso.');
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'pergunta' => 'required|string|max:255',
            'tipo' => 'required|string',
        ]);

        $data['obrigatorio'] = $request->input('resposta_obrigatoria', false);
        $data['usa_fatores_satisfacao'] = $request->input('usa_fatores', false);

        $data['updated_by'] = auth()->id();
        $pergunta = FormularioPergunta::findOrFail($id);
        $pergunta->update($data);

        return response()->json(['success' => true]);
    }

    public function show($id)
    {
        return FormularioPergunta::findOrFail($id);
    }

}
