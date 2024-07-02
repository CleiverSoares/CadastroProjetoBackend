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
        Schema::create('categoria_pessoa', function (Blueprint $table) {
            $table->id('id_categoria_pessoa');

            $table->unsignedBigInteger('id_pessoa');
            $table->unsignedBigInteger('id_categoria');

            $table->foreign('id_pessoa')
                ->references('id_pessoa')
                ->on('pessoa')
                ->onDelete('cascade'); // Definindo a regra de cascata

            $table->foreign('id_categoria')
                ->references('id_categoria')
                ->on('categoria');

            // Outros campos, se houver...
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categoria_pessoa');
    }
};
