<?php

namespace App\Http\Controllers;

use App\Http\Requests\CadastrarPessoaRequest;
use App\Models\CategoriaPessoaModel;
use App\Models\PessoaModel;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
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

            // Verifica se a pessoa tem uma foto e se o arquivo existe
            if ($pessoa->foto_pessoa && file_exists(public_path($pessoa->foto_pessoa))) {
                // Remove a foto do diretório
                unlink(public_path($pessoa->foto_pessoa));
            }

            // Remove a pessoa do banco de dados
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

    public function atualizar(Request $request, $id)
    {
        try {
            // Obter a pessoa pelo ID
            $pessoa = PessoaModel::findOrFail($id);

            // Manter o caminho da foto atual
            $fotoPath = $pessoa->foto_pessoa;

            // Verifica se a foto está presente no request
            if ($request->has('foto') && !empty($request->input('foto'))) {
                // Deletar a foto antiga, se existir
                if ($fotoPath && File::exists(public_path($fotoPath))) {
                    File::delete(public_path($fotoPath));
                }

                // Processar a nova foto
                $fotoBase64 = $request->input('foto');
                $foto = base64_decode($fotoBase64);
                $fotoPath = 'storage/fotos/' . Str::uuid() . '.jpg';

                if (!File::isDirectory(public_path('storage/fotos'))) {
                    File::makeDirectory(public_path('storage/fotos'), 0755, true, true);
                }

                file_put_contents(public_path($fotoPath), $foto);
            }

            // Atualizar os dados da pessoa
            $dadosParaAtualizar = [];

            // Adicionar campos que devem ser atualizados, se presentes no request
            if ($request->has('nome')) {
                $dadosParaAtualizar['nome_pessoa'] = $request->nome;
            }
            if ($request->has('telefone')) {
                $dadosParaAtualizar['telefone_pessoa'] = $request->telefone;
            }
            if ($request->has('data')) {
                $dadosParaAtualizar['data_nasc_pessoa'] = Carbon::createFromFormat('Y-m-d', $request->data);
            }
            if ($request->has('email')) {
                $dadosParaAtualizar['email_pessoa'] = $request->email;
            }
            if ($request->has('cpf')) {
                $dadosParaAtualizar['cpf_pessoa'] = $request->cpf;
            }

            // Atualizar o caminho da foto, se foi alterado
            $dadosParaAtualizar['foto_pessoa'] = $fotoPath;

            // Atualizar os dados da pessoa, se houver alterações
            if (!empty($dadosParaAtualizar)) {
                $pessoa->update($dadosParaAtualizar);
            }

            // Atualizar as categorias, se fornecidas
            if ($request->has('categoria')) {
                // Remover categorias antigas
                $deletedCount = CategoriaPessoaModel::where('id_pessoa', $pessoa->id_pessoa)->delete();
                Log::info("Categorias removidas: {$deletedCount} para a pessoa: {$pessoa->id_pessoa}");

                // Adicionar novas categorias
                foreach ($request->categoria as $idCategoria) {
                    CategoriaPessoaModel::create([
                        "id_pessoa" => $pessoa->id_pessoa,
                        "id_categoria" => $idCategoria
                    ]);
                    Log::info("Categoria adicionada: {$idCategoria} para a pessoa: {$pessoa->id_pessoa}");
                }
            }

            return response()->json(['message' => 'Pessoa atualizada com sucesso'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Pessoa não encontrada'], 404);
        } catch (\Exception $e) {
            Log::error('Erro ao atualizar pessoa: ' . $e->getMessage()); // Registrar erro para depuração
            return response()->json(['error' => 'Erro ao atualizar pessoa.'], 500);
        }
    }
}
