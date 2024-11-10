<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pessoa', function (Blueprint $table) {
            $table->id('id_pessoa');
            $table->uuid('uuid_pessoa');
            $table->string('nome_pessoa', 250);
            $table->string('telefone_pessoa', 20);
            $table->string('email_pessoa', 250);
            $table->string('observacoes_pessoa', 1000);
            $table->string('cpf_pessoa', 14);
            $table->string('foto_pessoa')->nullable();
            $table->date('data_nasc_pessoa');
            $table->boolean('ativo')->default(true);
            $table->timestamps();

            // Novos campos para a pessoa
            $table->string('alguem_trabalha')->nullable();  // Campo 'alguem_trabalha'
            $table->date('data_entrada_projeto')->nullable(); // Campo 'data_entrada_projeto'
            $table->string('escolaridade')->nullable();  // Campo 'escolaridade'
            $table->integer('qtd_pessoas_na_casa')->nullable();  // Campo 'qtd_pessoas_na_casa'
            $table->string('telefone_emergencia', 20)->nullable();  // Campo 'telefone_emergencia'

            // Campos para deficiência
            $table->string('deficiencia_tem_deficiencia')->nullable();
            $table->string('deficiencia_qual_deficiencia')->nullable();

            // Campos para medicamento
            $table->string('medicamento_tem_alergia')->nullable();
            $table->string('medicamento_qual_medicamento_tem_alergia')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pessoa');
    }
};
