<?php

namespace App\Http\Controllers;

use App\Models\Formulario;
use App\Models\FormularioFatorSatisfacao;
use Illuminate\Http\Request;

class FormularioFatorSatisfacaoController extends Controller
{
    public function index(Request $request)
    {
        $fatores = FormularioFatorSatisfacao::where('formulario_id', $request->formulario_id)->get();

        return response()->json($fatores);
    }
    
    public function show($id)
    {
        $fator = FormularioFatorSatisfacao::findOrFail($id);

        return response()->json($fator);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'formulario_id' => 'required|exists:formularios,id',
            'titulo' => 'required|string|max:255',
        ]);
        
        $data['resposta_obrigatoria'] = $request->has('resposta_obrigatoria');
        $data['usa_input_extra'] = $request->has('usa_input_extra');
        $data['created_by'] = auth()->id();
        
        $fator = FormularioFatorSatisfacao::create($data);
        
        // retorna com json a resposta
        return response()->json([
            'success' => true,
            'message' => 'Opção adicionada com sucesso.',
            'data' => $fator,
        ]);
    }

    public function update(Request $request, FormularioFatorSatisfacao $fator)
    {    
        $data = $request->validate([
            'formulario_id' => 'required|exists:formularios,id',
            'titulo' => 'required|string|max:255',
            'resposta_obrigatoria' => 'boolean',
            'usa_input_extra' => 'boolean',
        ]);
    
        $data['updated_by'] = auth()->id();
    
        $fator->update($data);
    
        return response()->json([
            'success' => true,
            'message' => 'Fator atualizado com sucesso.',
            'data' => $fator
        ]);
    }

    public function destroy($id)
    {
        $fator = FormularioFatorSatisfacao::findOrFail($id);
        $fator->delete();

        return response()->json([
            'success' => true,
            'message' => 'Fator excluído com sucesso.',
        ]);
    }
}
