<?php

use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\FormularioController;
use App\Http\Controllers\NotificacaoController;
use App\Http\Controllers\PessoaController;
use App\Services\External\ContaService;
use App\Services\External\MitteProService;
use Illuminate\Support\Facades\Route;



Route::post('/cadastrar', [PessoaController::class, 'cadastrar'])->name('cadastrar');


Route::patch('/atualizar/{id}', [PessoaController::class, 'atualizar'])->name('atualizar');


Route::get('/buscar-todos', [PessoaController::class, 'buscarTodos'])->name('buscarTodos');







Route::get('/bairros-mais-escolhidos', [PessoaController::class, 'bairrosMaisEscolhidos'])->name('bairrosMaisEscolhidos');

Route::get('/alergia_medicamentos', [PessoaController::class, 'alergiaMedicamentos'])->name('alergia_medicamentos');

Route::get('/deficiencia', [PessoaController::class, 'temDeficiencia'])->name('temDeficiencia');

Route::get('/idade-por-faixa', [PessoaController::class, 'idadePorFaixa'])->name('idadePorFaixa');

Route::get('/escolaridade', [PessoaController::class, 'escolaridadeAgrupada'])->name('escolaridadeAgrupada');

Route::get('/alguem-trabalha', [PessoaController::class, 'alguemTrabalhaAgrupado'])->name('alguemTrabalhaAgrupado');

Route::get('/categorias-mais-selecionadas', [CategoriaController::class, 'buscarCategoriasSelecionadas']);


Route::get('/categorias', [CategoriaController::class, 'buscarCategorias'])->name('buscarCategorias');



Route::get('buscar-por-id/{id}', [PessoaController::class, 'buscarPorId'])->name('buscar-por-id');


Route::put('atualizar/{id}', [PessoaController::class, 'atualizar'])->name('atualizar');
Route::delete('excluir/{id}', [PessoaController::class, 'excluir'])->name('excluir');
Route::put('desativar/{id}', [PessoaController::class, 'desativar'])->name('desativar');
