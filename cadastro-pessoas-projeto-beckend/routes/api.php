<?php
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PessoaController;
use App\Http\Controllers\CategoriaController;
use Illuminate\Support\Facades\Route;

// Autenticação
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
Route::middleware('auth:sanctum')->get('/buscar-todos', [PessoaController::class, 'buscarTodos']);

// Rotas protegidas
Route::middleware('auth:sanctum')->group(function () {
    Route::patch('/atualizar/{id}', [PessoaController::class, 'atualizar']);
    Route::get('/buscar-todos', [PessoaController::class, 'buscarTodos']);
    Route::get('/categorias', [CategoriaController::class, 'buscarCategorias']);
    Route::get('/categorias-mais-selecionadas', [CategoriaController::class, 'buscarCategoriasSelecionadas']);
    Route::get('buscar-por-id/{id}', [PessoaController::class, 'buscarPorId']);
    Route::put('atualizar/{id}', [PessoaController::class, 'atualizar']);
    Route::delete('excluir/{id}', [PessoaController::class, 'excluir']);
    Route::put('desativar/{id}', [PessoaController::class, 'desativar']);
});
Route::get('/test', function () {
    return 'Rota de teste funcionando!';
});
