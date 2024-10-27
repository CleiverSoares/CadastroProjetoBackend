<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('categoria')->insert([
            [
                'id_categoria' => 1,
                'nome_categoria' => 'Futebol Campo',
            ],
            [
                'id_categoria' => 2,
                'nome_categoria' => 'Futebol Salão',
            ],
            [
                'id_categoria' => 3,
                'nome_categoria' => 'Capoeira',
            ],
            [
                'id_categoria' => 4,
                'nome_categoria' => 'Boxe',
            ],
            [
                'id_categoria' => 5,
                'nome_categoria' => 'Jiu-jítsu',
            ],
            [
                'id_categoria' => 6,
                'nome_categoria' => 'Fisioterapia',
            ],
            [
                'id_categoria' => 7,
                'nome_categoria' => 'Massoterapia',
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('categoria')->whereIn('id_categoria', [1, 2, 3, 4, 5, 6, 7])->delete();
    }
};
