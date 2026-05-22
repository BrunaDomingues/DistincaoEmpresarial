<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FormularioPasso;
use App\Models\FormularioPergunta;
use App\Models\FormularioOpcao;
use Illuminate\Support\Facades\Auth;

class FormularioPassoController extends Controller
{
    public function index(Request $request)
    {
        $passos = FormularioPasso::with(['perguntas.opcoes'])
        ->where('formulario_id', $request->formulario_id)
        ->orderBy('ordem')
        ->get();

        return response()->json($passos);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'formulario_id' => 'required|exists:formularios,id',
            'titulo' => 'required|string|max:255',
            'ordem' => 'required|integer|min:1'
        ]);

        $validated['created_by'] = Auth::id();
        $validated['updated_by'] = Auth::id();

        $grupo = FormularioPasso::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Grupo adicionado com sucesso.',
            'data' => $grupo,
        ]);
    }

    public function destroy($id)
    {
        $passo = FormularioPasso::findOrFail($id);
        $passo->delete();

        return response()->json(['success' => true]);
    }

    public function ordenar(Request $request)
    {
        $ordens = $request->input('ordens');
        foreach ($ordens as $index => $id) {
            FormularioPasso::where('id', $id)->update(['ordem' => $index + 1]);
        }
        return response()->json(['success' => true]);
    }

    public function update(Request $request, $id)
    {
        // Validação dos campos recebidos
        $validated = $request->validate([
            'titulo' => 'required|string|max:255',
            'ordem' => 'required|integer',
        ]);

        // Encontrar o passo
        $passo = FormularioPasso::findOrFail($id);

        // Atualizar os dados
        $passo->update([
            'titulo' => $validated['titulo'],
            'ordem' => $validated['ordem'],
        ]);

        // Redirecionar de volta com mensagem de sucesso
        return redirect()->back()->with('success', 'Grupo atualizado com sucesso!');
    }
    
}
