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

Route::get('/categorias', [CategoriaController::class, 'buscarCategorias'])->name('buscarCategorias');

Route::get('/categorias-selecionadas', [CategoriaController::class, 'categoriasPessoas'])->name('categorias-pessoa');


Route::get('buscar-por-id/{id}', [PessoaController::class, 'buscarPorId'])->name('buscar-por-id');


Route::put('atualizar/{id}', [PessoaController::class, 'atualizar'])->name('atualizar');
Route::delete('excluir/{id}', [PessoaController::class, 'excluir'])->name('excluir');
Route::put('desativar/{id}', [PessoaController::class, 'desativar'])->name('desativar');
