<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EnderecoModel extends Model
{
    use HasFactory;

    protected $table = 'endereco';
    protected $primaryKey = 'id_endereco';

    protected $fillable = [
        'id_pessoa',
        'rua',
        'cidade',
        'estado',
        'cep',
        'pais',
        'numero',
        'bairro'
    ];

    public function pessoa()
    {
        return $this->belongsTo(PessoaModel::class, 'id_pessoa');
    }
}
