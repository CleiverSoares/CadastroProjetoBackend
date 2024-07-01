<?php

namespace App\Http\Controllers;

use App\Http\Requests\CadastrarPessoaRequest;
use App\Models\CategoriaPessoaModel;
use App\Models\PessoaModel;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PessoaController extends Controller
{
    public function cadastrar(CadastrarPessoaRequest $request)
    {
        $fotoPath = null;
        if ($request->has('foto')) {
            // Decodifica a imagem Base64 recebida
            $fotoBase64 = $request->input('foto');
            $foto = base64_decode($fotoBase64);

            // Define o caminho para armazenar a imagem
            $fotoPath = 'storage/fotos/' . Str::uuid() . '.jpg'; // ou .png, dependendo do formato

            // Salva a imagem no diretório de armazenamento
            file_put_contents(public_path($fotoPath), $foto);
        }

        // Criação da pessoa no banco de dados
        $pessoa = PessoaModel::create([
            "uuid_pessoa" => Str::uuid(),
            "nome_pessoa" => $request->nome,
            "telefone_pessoa" => $request->telefone,
            "data_nasc_pessoa" => $request->data,
            "email_pessoa" => $request->email,
            "cpf_pessoa" => $request->cpf,
            "foto_pessoa" => $fotoPath, // Caminho da foto no servidor
        ]);

        // Associação das categorias de interesse
        foreach ($request->categoria as $idCategoria) {
            CategoriaPessoaModel::create([
                "id_pessoa" => $pessoa->id_pessoa,
                "id_categoria" => $idCategoria
            ]);
        }
    }
    public function buscarTodos()
    {
        $pessoas = PessoaModel::with('categoriasInteresse')->get();
        return response()->json($pessoas);
    }
    public function excluir($id)
    {
        try {
            $pessoa = PessoaModel::findOrFail($id);
            $pessoa->delete();

            return response()->json(['message' => 'Pessoa excluída com sucesso'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Pessoa não encontrada'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro ao excluir pessoa: ' . $e->getMessage()], 500);
        }
    }

    public function desativar($id)
    {
        try {
            $pessoa = PessoaModel::findOrFail($id);
            $pessoa->ativo = false; // Supondo que 'ativo' seja um campo booleano

            $pessoa->save();

            return response()->json(['message' => 'Pessoa desativada com sucesso'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Pessoa não encontrada'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro ao desativar pessoa: ' . $e->getMessage()], 500);
        }
    }

}
