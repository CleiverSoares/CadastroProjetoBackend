<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\PessoaModel;
use App\Models\CategoriaPessoaModel;

class PessoaTest extends TestCase
{
    use RefreshDatabase;


    public function test_cadastro_pessoa(): void
    {
        $dadosPessoa = [
            'nome' => 'João Silva',
            'email' => 'joao@gmail.com',
            'telefone' => '21966525932',
            'categoria' => [1, 2]
        ];

        $response = $this->post('/cadastro', $dadosPessoa);

        $response->assertStatus(302);

        $this->assertDatabaseHas('pessoas', [
            'email_pessoa' => 'joao@gmail.com'
        ]);

        $pessoa = PessoaModel::where('email_pessoa', 'joao@gmail.com')->first();
        foreach ($dadosPessoa['categoria'] as $idCategoria) {
            $this->assertDatabaseHas('categoria_pessoa', [
                'id_pessoa' => $pessoa->id_pessoa,
                'id_categoria' => $idCategoria
            ]);
        }
    }
}
