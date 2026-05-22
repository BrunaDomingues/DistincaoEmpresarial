<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FormularioOpcao;
use Illuminate\Support\Facades\Auth;

class FormularioOpcaoController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'pergunta_id' => 'required|exists:formulario_perguntas,id',
            'opcao' => 'required|string|max:255'
        ]);

        $validated['created_by'] = Auth::id();
        $validated['updated_by'] = Auth::id();

        $opcao = FormularioOpcao::create($validated);

        return response()->json(['success' => true, 'opcao' => $opcao]);
    }

    public function destroy($id)
    {
        $opcao = FormularioOpcao::findOrFail($id);
        $opcao->delete();

        return response()->json(['success' => true]);
    }

    public function update(Request $request, FormularioOpcao $opcao)
    {
        $data = $request->validate([
            'opcao' => 'required|string|max:255',
        ]);

        $data['updated_by'] = auth()->id();

        $opcao->update($data);

        return response()->json(['success' => true]);
    }

}
