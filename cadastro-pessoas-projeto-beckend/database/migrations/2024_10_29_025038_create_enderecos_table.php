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
        Schema::create('endereco', function (Blueprint $table) {
            $table->id('id_endereco');
            $table->unsignedBigInteger('id_pessoa');
            $table->string('rua');
            $table->string('cidade');
            $table->string('estado');
            $table->string('cep');
            $table->string('numero');
            $table->string('bairro');
            $table->string('pais');
            $table->timestamps();

            $table->foreign('id_pessoa')->references('id_pessoa')->on('pessoa')->onDelete('cascade');
        });

    }

    public function down()
    {
        Schema::dropIfExists('endereco');
    }
};
