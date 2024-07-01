<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoriaPessoaModel extends Model
{
    use HasFactory;
    public $timestamps = false;
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'categoria_pessoa';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id_categoria_pessoa';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

     protected $fillable = [
        'nome_pessoa',
        'telefone_pessoa',
        'email_pessoa',
        'ativo',
        'id_pessoa',
        'id_categoria'
    ];
}
