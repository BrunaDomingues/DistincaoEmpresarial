<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FormularioController;
use App\Http\Controllers\FormularioPassoController;
use App\Http\Controllers\FormularioPerguntaController;
use App\Http\Controllers\FormularioOpcaoController;
use App\Http\Controllers\RespostaController;
use App\Http\Controllers\RespostaTratadaController;
use App\Http\Controllers\FormularioFatorSatisfacaoController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RelatorioAplicadoresController;
use App\Http\Controllers\RelatorioRespondentesPorBairroController;
use App\Http\Controllers\FormularioEnvioController;
use App\Http\Controllers\RelatorioClassificacaoController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::prefix('relatorios')->middleware(['auth','admin'])->group(function () {
    Route::get('/bairros', [RelatorioRespondentesPorBairroController::class, 'index'])
            ->name('relatorios.bairros');
    
    Route::get('/bairros/export', [RelatorioRespondentesPorBairroController::class, 'export'])
            ->name('relatorios.bairros.export');

    Route::get('/aplicadores', [RelatorioAplicadoresController::class, 'index'])
            ->name('relatorios.aplicadores');

    Route::get('/aplicadores/exportar', [RelatorioAplicadoresController::class, 'exportar'])
        ->name('relatorios.aplicadores.exportar');

    Route::get('/aplicadores-acumulado', [RelatorioAplicadoresController::class, 'acumulado'])
        ->name('relatorios.aplicadores.acumulado');

    Route::get('/aplicadores-acumulado/exportar', [RelatorioAplicadoresController::class, 'exportarAcumulado'])
        ->name('relatorios.aplicadores.acumulado.exportar');

    Route::get('/classificacao', [RelatorioClassificacaoController::class, 'classificacao'])->name('relatorios.classificacao');
    Route::post('/classificacao/filtro', [RelatorioClassificacaoController::class, 'classificacaoFiltrar'])->name('relatorios.classificacao.filtrar');

});

Route::get('/envios-por-usuario', [DashboardController::class, 'enviosPorUsuario'])->middleware(['auth', 'verified']);

Route::middleware('auth')->get('/csrf-token', function () {
    return response()->json(['token' => csrf_token()]);
});

Route::get('/check-auth', function () {
    return response()->json(['authenticated' => auth()->check()]);
});

Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    Route::resource('formularios', FormularioController::class);
    Route::get('formularios/{formulario}/parametrizar', [FormularioController::class, 'parametrizar'])->name('formularios.parametrizar');

    Route::get('/envios-validacao', [FormularioEnvioController::class, 'indexValidados'])->name('validar-envios.index');
    Route::get('/envios-validacao/{id}', [FormularioEnvioController::class, 'analisar'])->name('validar.envio');
    Route::put('/envios-validacao/{id}', [FormularioEnvioController::class, 'store'])->name('validar-envios.store');
});

Route::middleware(['auth'])->group(function () {
    
    Route::post('formulario-passos', [FormularioPassoController::class, 'store'])->name('formulario-passos.store');
    Route::put('/formulario-passos/{id}', [FormularioPassoController::class, 'update'])->name('formulario-passos.update');
    Route::delete('/formulario-passos/{id}', [FormularioPassoController::class, 'destroy'])->name('formulario-passos.destroy');
    Route::get('/formulario-passos', [FormularioPassoController::class, 'index'])->name('formulario-passos.index');

    Route::post('formulario-perguntas', [FormularioPerguntaController::class, 'store'])->name('formulario-perguntas.store');
    Route::put('/formulario-perguntas/{id}', [FormularioPerguntaController::class, 'update'])->name('formulario-perguntas.update');
    Route::delete('/formulario-perguntas/{id}', [FormularioPerguntaController::class, 'destroy'])->name('formulario-perguntas.destroy');
    Route::get('/formulario-perguntas', [FormularioPerguntaController::class, 'index'])->name('formulario-perguntas.index');
    Route::get('/formulario-perguntas/{id}', [FormularioPerguntaController::class, 'show'])->name('formulario-perguntas.show');

    Route::post('/formulario-opcoes', [FormularioOpcaoController::class, 'store'])->name('formulario-opcoes.store');
    Route::put('/formulario-opcoes/{opcao}', [FormularioOpcaoController::class, 'update'])->name('formulario-opcoes.update');
    Route::delete('/formulario-opcoes/{id}', [FormularioOpcaoController::class, 'destroy'])->name('formulario-opcoes.destroy');

    Route::post('/formulario-passos/ordenar', [FormularioPassoController::class, 'ordenar'])->name('formulario-passos.ordenar');
    Route::post('/formularios/{formulario}/responder', [RespostaController::class, 'store'])->name('respostas.store');
    Route::put('/respostas-tratadas/{respostaTratada}', [RespostaTratadaController::class, 'update'])->name('respostas-tratadas.update');
    Route::get('/respostas-tratadas', [RespostaTratadaController::class, 'index'])->name('respostas-tratadas.index');
    Route::get('/respostas-tratadas/{id}/dados', [RespostaTratadaController::class, 'dados']);

    Route::post('/formulario-fatores', [FormularioFatorSatisfacaoController::class, 'store'])->name('formulario-fatores.store');
    Route::put('/formulario-fatores/{fator}', [FormularioFatorSatisfacaoController::class, 'update'])->name('formulario-fatores.update');
    Route::delete('/formulario-fatores/{id}', [FormularioFatorSatisfacaoController::class, 'destroy'])->name('formulario-fatores.destroy');
    Route::get('/formulario-fatores', [FormularioFatorSatisfacaoController::class, 'index'])->name('formulario-fatores.index');
    Route::get('/formulario-fatores/{id}', [FormularioFatorSatisfacaoController::class, 'show']);


    Route::post('/formularios/{formulario}/respostas', [RespostaController::class, 'store'])->name('respostas.store');
    Route::get('/responder-formularios', [App\Http\Controllers\ResponderFormularioController::class, 'index'])->name('responder-formularios.index');
    Route::get('/respostas/{formulario}/responder', [App\Http\Controllers\RespostaController::class, 'create'])->name('respostas.create'); 
});

Route::middleware(['auth', 'verified', 'admin'])->group(function () {
    Route::resource('users', UserController::class);
    Route::resource('users', UserController::class)->except(['show']);
});

require __DIR__.'/auth.php';
