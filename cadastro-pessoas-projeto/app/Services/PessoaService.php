<?php

namespace App\Services;

use App\Models\PessoaModel;
use Illuminate\Support\Str;

class PessoaService
{
    public function confirmarCadastroPessoa($uuidPessoa, $codigoVerificacao)
    {
        $pessoa = PessoaModel::where('uuid_pessoa', $uuidPessoa)->first();

        if ($pessoa && $pessoa->codigo_verificacao === $codigoVerificacao) {
            $pessoa->ativo = true;
            $pessoa->save();
            return true;
        }

        return false;
    }

    public function gerarNumeroSeisDigitos()
    {
        $numero = rand(100000, 999999);
        $numero_formatado = str_pad($numero, 6, '0', STR_PAD_LEFT);
        return $numero_formatado;
    }
}
