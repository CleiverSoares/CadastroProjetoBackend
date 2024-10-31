<?php

namespace App\Http\Controllers;

use App\Http\Requests\CadastrarPessoaRequest;
use App\Models\CategoriaModel;
use App\Models\CategoriaPessoaModel;
use App\Models\EnderecoModel;
use App\Models\PessoaModel;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File; // Importar a facade File
use Illuminate\Support\Str;

class PessoaController extends Controller
{
    public function cadastrar(Request $request)
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
            "observacoes_pessoa" => $request->observacoes,
            "cpf_pessoa" => $request->cpf,
            "foto_pessoa" => $fotoPath,
        ]);

        foreach ($request->categoria as $idCategoria) {
            CategoriaPessoaModel::create([
                "id_pessoa" => $pessoa->id_pessoa,
                "id_categoria" => $idCategoria
            ]);
        }

        if ($request->has('endereco')) {
            EnderecoModel::create([
                "id_pessoa" => $pessoa->id_pessoa,
                "rua" => $request->endereco['rua'],
                "cidade" => $request->endereco['cidade'],
                "estado" => $request->endereco['estado'],
                "cep" => $request->endereco['cep'],
                "pais" => $request->endereco['pais'],
                "numero" => $request->endereco['numero'],
                "bairro" => $request->endereco['bairro'],
            ]);
        }

        return response()->json(['message' => 'Pessoa e endereço cadastrados com sucesso'], 201);
    }


    public function atualizar(Request $request, $id)
    {

        try {
            $pessoa = PessoaModel::findOrFail($id);

            $fotoPath = $pessoa->foto_pessoa;

            if ($request->has('foto') && !empty($request->input('foto'))) {
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

            $dadosParaAtualizar = [];

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
            if ($request->has('observacoes')) {
                $dadosParaAtualizar['observacoes_pessoa'] = $request->observacoes;
            }

            $dadosParaAtualizar['foto_pessoa'] = $fotoPath;

            if (!empty($dadosParaAtualizar)) {
                $pessoa->update($dadosParaAtualizar);
            }

            if ($request->has('endereco')) {
                $enderecoData = $request->input('endereco');

                $endereco = EnderecoModel::firstOrNew(['id_pessoa' => $pessoa->id_pessoa]);

                $endereco->rua = $enderecoData['rua'] ?? $endereco->rua;
                $endereco->cidade = $enderecoData['cidade'] ?? $endereco->cidade;
                $endereco->estado = $enderecoData['estado'] ?? $endereco->estado;
                $endereco->cep = $enderecoData['cep'] ?? $endereco->cep;
                $endereco->pais = $enderecoData['pais'] ?? $endereco->pais;
                $endereco->bairro = $enderecoData['bairro'] ?? $endereco->bairro;
                $endereco->numero = $enderecoData['numero'] ?? $endereco->numero;

                $endereco->save();
            }

            if ($request->has('categoria')) {
                $deletedCount = CategoriaPessoaModel::where('id_pessoa', $pessoa->id_pessoa)->delete();
                Log::info("Categorias removidas: {$deletedCount} para a pessoa: {$pessoa->id_pessoa}");

                foreach ($request->categoria as $idCategoria) {
                    CategoriaPessoaModel::create([
                        "id_pessoa" => $pessoa->id_pessoa,
                        "id_categoria" => $idCategoria
                    ]);
                    Log::info("Categoria adicionada: {$idCategoria} para a pessoa: {$pessoa->id_pessoa}");
                }
            }

            return response()->json(['message' => 'Pessoa e endereço atualizados com sucesso'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Pessoa não encontrada'], 404);
        } catch (\Exception $e) {
            Log::error('Erro ao atualizar pessoa: ' . $e->getMessage());
            return response()->json(['error' => 'Erro ao atualizar pessoa.'], 500);
        }
    }

    public function buscarTodos()
    {
        $pessoas = PessoaModel::with(['categoriasInteresse', 'endereco'])->get();
        return response()->json($pessoas);
    }


    public function excluir($id)
    {
        try {
            $pessoa = PessoaModel::findOrFail($id);

            if ($pessoa->foto_pessoa && file_exists(public_path($pessoa->foto_pessoa))) {
                unlink(public_path($pessoa->foto_pessoa));
            }

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

  


}
