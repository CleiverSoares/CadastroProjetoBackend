<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('categoria')->insert([
    array(
                'id_categoria' => 1,
                'nome_categoria' => 'Futebol',
            ),
            array(
                'id_categoria' => 2,
                'nome_categoria' => 'Jiu-jítsu',
            ),
            array(
                'id_categoria' => 3,
                'nome_categoria' => 'Capoeira',
            ),
            array(
                'id_categoria' => 4,
                'nome_categoria' => 'kickboxing',
            ),
            array(
                'id_categoria' => 5,
                'nome_categoria' => 'Zumba',
            ),


        ]);
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
