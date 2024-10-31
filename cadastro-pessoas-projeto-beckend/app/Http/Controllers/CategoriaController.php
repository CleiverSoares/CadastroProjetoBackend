<?php

namespace App\Http\Controllers;

use App\Models\CategoriaModel;
use App\Models\PessoaModel;
use Illuminate\Support\Facades\DB;

class CategoriaController extends Controller
{
    public function buscarCategorias()
    {
        $categorias = CategoriaModel::all();
        return response()->json($categorias);
    }
    public function buscarCategoriasSelecionadas()
    {
        $categoriasSelecionadas = CategoriaModel::withCount('pessoas')
            ->orderBy('pessoas_count', 'desc')
            ->get();

        $resultado = $categoriasSelecionadas->map(function ($categoria) {
            return [
                'id_categoria' => $categoria->id_categoria,
                'nome_categoria' => $categoria->nome_categoria,
                'quantidade_selecionada' => $categoria->pessoas_count,
            ];
        });

        return response()->json($resultado);
    }


    public function categoriasPessoas()
    {
        $categorias = CategoriaModel::select('categoria.id_categoria', 'categoria.nome_categoria')
            ->selectRaw('COUNT(categoria_pessoa.id_pessoa) as quantidade_pessoas')
            ->leftJoin('categoria_pessoa', 'categoria.id_categoria', '=', 'categoria_pessoa.id_categoria')
            ->groupBy('categoria.id_categoria', 'categoria.nome_categoria')
            ->orderByDesc('quantidade_pessoas')
            ->get();

        return response()->json($categorias);
    }

}
