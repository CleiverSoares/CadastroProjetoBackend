<?php

namespace App\Http\Controllers;

use App\Http\Requests\CadastrarPessoaRequest;
use App\Models\CategoriaPessoaModel;
use App\Models\PessoaModel;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File; // Importar a facade File
use Illuminate\Support\Str;

class PessoaController extends Controller
{
    public function cadastrar(CadastrarPessoaRequest $request)
    {
        $fotoPath = null;
        if ($request->has('foto')) {
            $fotoBase64 = $request->input('foto');
            $foto = base64_decode($fotoBase64);

            $fotoPath = 'storage/fotos/' . Str::uuid() . '.jpg';

            if (!File::isDirectory(public_path('storage/fotos'))) {
                File::makeDirectory(public_path('storage/fotos'), 0755, true, true);
            }

            file_put_contents(public_path($fotoPath), $foto);
        }

        $pessoa = PessoaModel::create([
            "uuid_pessoa" => Str::uuid(),
            "nome_pessoa" => $request->nome,
            "telefone_pessoa" => $request->telefone,
            "data_nasc_pessoa" => $request->data,
            "email_pessoa" => $request->email,
            "cpf_pessoa" => $request->cpf,
            "foto_pessoa" => $fotoPath,
        ]);

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
            $pessoa->ativo = false;

            $pessoa->save();

            return response()->json(['message' => 'Pessoa desativada com sucesso'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Pessoa não encontrada'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro ao desativar pessoa: ' . $e->getMessage()], 500);
        }
    }

    public function atualizar(CadastrarPessoaRequest $request, $id)
    {
        try {
            $pessoa = PessoaModel::findOrFail($id);

            $fotoPath = $pessoa->foto_pessoa; // Manter o caminho da foto atual
            if ($request->has('foto')) {
                // Deletar a foto antiga, se existir
                if ($fotoPath && File::exists(public_path($fotoPath))) {
                    File::delete(public_path($fotoPath));
                }

                $fotoBase64 = $request->input('foto');
                $foto = base64_decode($fotoBase64);

                $fotoPath = 'storage/fotos/' . Str::uuid() . '.jpg';

                if (!File::isDirectory(public_path('storage/fotos'))) {
                    File::makeDirectory(public_path('storage/fotos'), 0755, true, true);
                }

                file_put_contents(public_path($fotoPath), $foto);
            }

            $pessoa->update([
                "nome_pessoa" => $request->nome,
                "telefone_pessoa" => $request->telefone,
                "data_nasc_pessoa" => $request->data,
                "email_pessoa" => $request->email,
                "cpf_pessoa" => $request->cpf,
                "foto_pessoa" => $fotoPath,
            ]);

            // Atualizar as categorias, se fornecidas
            if ($request->has('categoria')) {
                // Remover categorias antigas
                CategoriaPessoaModel::where('id_pessoa', $pessoa->id_pessoa)->delete();

                // Adicionar novas categorias
                foreach ($request->categoria as $idCategoria) {
                    CategoriaPessoaModel::create([
                        "id_pessoa" => $pessoa->id_pessoa,
                        "id_categoria" => $idCategoria
                    ]);
                }
            }

            return response()->json(['message' => 'Pessoa atualizada com sucesso'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Pessoa não encontrada'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro ao atualizar pessoa: ' . $e->getMessage()], 500);
        }
    }
}
