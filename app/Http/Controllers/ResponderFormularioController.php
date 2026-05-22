<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Formulario;

class ResponderFormularioController extends Controller
{

    public function index()
    {
        $formularios = Formulario::disponivel()->orderBy('titulo')->get();

        return view('formularios.responder.index', compact('formularios'));
    }
}
