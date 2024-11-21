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
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class PessoaController extends Controller
{
    public function cadastrar(Request $request)
    {
        // Verificar todos os dados recebidos
        $fotoPath = null;

        // Lógica para salvar a foto
        if ($request->has('foto')) {
            $fotoBase64 = $request->input('foto');
            $foto = base64_decode($fotoBase64);
            $fotoPath = 'storage/fotos/' . Str::uuid() . '.jpg';

            if (!File::isDirectory(public_path('storage/fotos'))) {
                File::makeDirectory(public_path('storage/fotos'), 0755, true, true);
            }

            file_put_contents(public_path($fotoPath), $foto);
        }

        // Criando a pessoa
        $pessoa = PessoaModel::create([
            "uuid_pessoa" => Str::uuid(),
            "nome_pessoa" => $request->nome,
            "telefone_pessoa" => $request->telefone,
            "data_nasc_pessoa" => $request->data,
            "email_pessoa" => $request->email,
            "observacoes_pessoa" => $request->observacoes,
            "cpf_pessoa" => $request->cpf,
            "foto_pessoa" => $fotoPath,
            "alguem_trabalha" => $request->alguem_trabalha,
            "data_entrada_projeto" => $request->data_entrada_projeto,
            "escolaridade" => $request->escolaridade,
            "qtd_pessoas_na_casa" => $request->qtd_pessoas_na_casa,
            "telefone_emergencia" => $request->telefone_emergencia,
            "deficiencia_tem_deficiencia" => $request->deficiencia['tem_deficiencia'],
            "deficiencia_qual_deficiencia" => $request->deficiencia['qual_deficiencia'],
            "medicamento_tem_alergia" => $request->medicamento['tem_alergia'],
            "medicamento_qual_medicamento_tem_alergia" => $request->medicamento['qual_medicamento_tem_alergia']
        ]);

        // Criando as categorias associadas à pessoa
        if ($request->has('categoria')) {
            foreach ($request->categoria as $idCategoria) {
                CategoriaPessoaModel::create([
                    "id_pessoa" => $pessoa->id_pessoa,
                    "id_categoria" => $idCategoria
                ]);
            }
        }

        // Salvando o endereço, incluindo o complemento
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
                "complemento_endereco" => $request->endereco['complemento_endereco'] ?? '', // Adicionando o complemento
            ]);
        }

        return response()->json(['message' => 'Pessoa e endereço cadastrados com sucesso'], 201);
    }

    public function atualizar(Request $request, $id)
    {
        try {
            $pessoa = PessoaModel::findOrFail($id);
            $fotoPath = $pessoa->foto_pessoa;

            // Lógica para atualizar a foto
            if ($request->has('foto') && !empty($request->input('foto'))) {
                if ($fotoPath && file_exists(public_path($fotoPath))) {
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

            $dadosParaAtualizar = ['foto_pessoa' => $fotoPath];

            $campos = [
                'nome_pessoa' => 'nome',
                'telefone_pessoa' => 'telefone',
                'data_nasc_pessoa' => 'data',
                'email_pessoa' => 'email',
                'cpf_pessoa' => 'cpf',
                'observacoes_pessoa' => 'observacoes',
                'alguem_trabalha' => 'alguem_trabalha',
                'data_entrada_projeto' => 'data_entrada_projeto',
                'escolaridade' => 'escolaridade',
                'qtd_pessoas_na_casa' => 'qtd_pessoas_na_casa',
                'telefone_emergencia' => 'telefone_emergencia'
            ];

            foreach ($campos as $campoBD => $campoRequest) {
                if ($request->has($campoRequest)) {
                    $dadosParaAtualizar[$campoBD] = $request->input($campoRequest);
                }
            }

            $pessoa->update($dadosParaAtualizar);

            if ($request->has('endereco')) {
                $enderecoData = $request->input('endereco');
                $endereco = EnderecoModel::firstOrNew(['id_pessoa' => $pessoa->id_pessoa]);
                $endereco->fill($enderecoData)->save();
            }

            if ($request->has('categoria') && is_array($request->categoria)) {
                CategoriaPessoaModel::where('id_pessoa', $pessoa->id_pessoa)->delete();
                foreach ($request->categoria as $idCategoria) {
                    CategoriaPessoaModel::create([
                        "id_pessoa" => $pessoa->id_pessoa,
                        "id_categoria" => $idCategoria
                    ]);
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
    public function bairrosMaisEscolhidos()
    {
        $todasPessoas = PessoaModel::with(['categoriasInteresse', 'endereco'])->get();

        if ($todasPessoas instanceof \Illuminate\Support\Collection) {
            $bairrosAgrupados = $todasPessoas->groupBy(function ($pessoa) {
                return $pessoa->endereco?->bairro ?? 'Sem Bairro';
            });

            $bairrosContagem = $bairrosAgrupados->map(function ($grupo) {
                return $grupo->count();
            });

            $resultado = $bairrosContagem->sortDesc();

            return response()->json($resultado);
        } else {
            return response()->json(['error' => 'Erro ao processar os dados'], 500);
        }
    }



    public function alergiaMedicamentos()
    {

        $todasPessoas = PessoaModel::with(['categoriasInteresse', 'endereco'])->get();


        if ($todasPessoas instanceof \Illuminate\Support\Collection) {

            $medicamentosAgrupados = $todasPessoas->groupBy(function ($pessoa) {

                return $pessoa->medicamento_tem_alergia ?? 'Não informado';
            });

            $medicamentosContagem = $medicamentosAgrupados->map(function ($grupo) {
                return $grupo->count();
            });

            $resultado = $medicamentosContagem->sortDesc();

            return response()->json($resultado);
        } else {
            return response()->json(['error' => 'Erro ao processar os dados'], 500);
        }
    }

    public function temDeficiencia()
    {
        $todasPessoas = PessoaModel::with(['categoriasInteresse', 'endereco'])->get();

        if ($todasPessoas instanceof \Illuminate\Support\Collection) {
            $deficienciasAgrupadas = $todasPessoas->groupBy(function ($pessoa) {
                return $pessoa->deficiencia_tem_deficiencia ?? 'Não informado';
            });

            $deficienciaContagem = $deficienciasAgrupadas->map(function ($grupo) {
                return $grupo->count();
            });

            $resultado = $deficienciaContagem->sortDesc();

            return response()->json($resultado);
        } else {
            return response()->json(['error' => 'Erro ao processar os dados'], 500);
        }
    }
    public function idadePorFaixa()
    {
        $todasPessoas = PessoaModel::with(['categoriasInteresse', 'endereco'])->get();


        if ($todasPessoas instanceof \Illuminate\Support\Collection) {

            $faixaEtariaAgrupada = $todasPessoas->groupBy(function ($pessoa) {
                $idade = $pessoa->idade;


                if ($idade >= 18 && $idade <= 25) {
                    return '18-25';
                } elseif ($idade >= 26 && $idade <= 35) {
                    return '26-35';
                } elseif ($idade >= 36 && $idade <= 45) {
                    return '36-45';
                } elseif ($idade >= 46 && $idade <= 60) {
                    return '46-60';
                } else {
                    return '60+';
                }
            });

            $faixaEtariaContagem = $faixaEtariaAgrupada->map(function ($grupo) {
                return $grupo->count();
            });


            $resultado = $faixaEtariaContagem->sortDesc();


            return response()->json($resultado);
        } else {

            return response()->json(['error' => 'Erro ao processar os dados'], 500);
        }
    }
    public function escolaridadeAgrupada()
    {

        $todasPessoas = PessoaModel::with(['categoriasInteresse', 'endereco'])->get();


        if ($todasPessoas instanceof \Illuminate\Support\Collection) {

            $escolaridadeAgrupada = $todasPessoas->groupBy(function ($pessoa) {
                return $pessoa->escolaridade;
            });

            $escolaridadeContagem = $escolaridadeAgrupada->map(function ($grupo) {
                return $grupo->count();
            });

            $resultado = $escolaridadeContagem->sortDesc();

            return response()->json($resultado);
        } else {
            return response()->json(['error' => 'Erro ao processar os dados'], 500);
        }
    }

    public function alguemTrabalhaAgrupado()
{

    $todasPessoas = PessoaModel::with(['categoriasInteresse', 'endereco'])->get();


    if ($todasPessoas instanceof \Illuminate\Support\Collection) {
        $alguemTrabalhaAgrupado = $todasPessoas->groupBy(function ($pessoa) {
            return $pessoa->alguem_trabalha;
        });

        $alguemTrabalhaContagem = $alguemTrabalhaAgrupado->map(function ($grupo) {
            return $grupo->count(); // Conta o número de pessoas em cada grupo
        });

        $resultado = $alguemTrabalhaContagem->sortDesc();

        return response()->json($resultado);
    } else {
        return response()->json(['error' => 'Erro ao processar os dados'], 500);
    }
}

    public function excluir($id)
    {
        try {
            $pessoa = PessoaModel::findOrFail($id);

            // Remover foto
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
