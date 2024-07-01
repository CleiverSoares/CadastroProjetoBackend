<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoriaModel extends Model
{
    use HasFactory;
    public $timestamps = false;
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'categoria';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id_categoria';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'nome_categoria',
    ];
    protected $hidden = ['pivot'];
    public function pessoas()
    {
        return $this->belongsToMany(PessoaModel::class, 'categoria_pessoa', 'id_categoria', 'id_pessoa');
    }
}
