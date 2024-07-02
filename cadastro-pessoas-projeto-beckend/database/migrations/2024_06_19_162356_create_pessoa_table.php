<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
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
            $table->string('cpf_pessoa', 14);
            $table->string('foto_pessoa')->nullable();
            $table->date('data_nasc_pessoa');
            $table->boolean('ativo')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pessoa');
        Schema::table('pessoa', function (Blueprint $table) {
            $table->dropColumn('codigo_verificacao');
        });
    }
};
