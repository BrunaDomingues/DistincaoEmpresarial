<?php
namespace App\Http\Controllers;

use App\Models\Formulario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FormularioController extends Controller
{
    public function index()
    {
        $formularios = Formulario::with(['criador', 'editor'])->latest()->paginate(10);
        return view('formularios.index', compact('formularios'));
    }

    public function create()
    {
        return view('formularios.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'titulo' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'data_inicio' => 'nullable|date',
            'data_fim' => 'nullable|date|after_or_equal:data_inicio',
        ]);

        $data['created_by'] = Auth::id();
        $data['updated_by'] = Auth::id();

        Formulario::create($data);

        return redirect()->route('formularios.index')->with('success', 'Formulário criado com sucesso.');
    }

    public function show(Formulario $formulario)
    {
        return view('formularios.show', compact('formulario'));
    }

    public function edit(Formulario $formulario)
    {
        return view('formularios.edit', compact('formulario'));
    }

    public function update(Request $request, Formulario $formulario)
    {
        $data = $request->validate([
            'titulo' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'data_inicio' => 'nullable|date',
            'data_fim' => 'nullable|date|after_or_equal:data_inicio',
        ]);

        $data['updated_by'] = Auth::id();

        $formulario->update($data);

        return redirect()->route('formularios.index')->with('success', 'Formulário atualizado com sucesso.');
    }

    public function destroy(Formulario $formulario)
    {
        $formulario->delete();

        return redirect()->route('formularios.index')->with('success', 'Formulário removido com sucesso.');
    }

    public function parametrizar(Formulario $formulario)
    {
        $passos = $formulario->passos()->with('perguntas.opcoes')->orderBy('ordem')->get();
        return view('formularios.parametrizar', compact('formulario', 'passos'));
    }

}
