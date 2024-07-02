<?php

namespace App\Http\Controllers;

use App\Models\CategoriaModel;
use Illuminate\Support\Facades\DB;

class CategoriaController extends Controller
{
    public function buscarCategorias()
    {
        $categorias = CategoriaModel::all();
        return response()->json($categorias);
    }

    public function categoriasPessoas()
    {
        // Consulta para contar quantas pessoas escolheram cada categoria
        $categorias = CategoriaModel::select('categoria.id_categoria', 'categoria.nome_categoria')
            ->selectRaw('COUNT(categoria_pessoa.id_pessoa) as quantidade_pessoas')
            ->leftJoin('categoria_pessoa', 'categoria.id_categoria', '=', 'categoria_pessoa.id_categoria')
            ->groupBy('categoria.id_categoria', 'categoria.nome_categoria')
            ->orderByDesc('quantidade_pessoas')
            ->get();

        return response()->json($categorias);
    }

}
