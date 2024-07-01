<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PessoaModel extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'pessoa';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id_pessoa';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id_pessoa',
        'uuid_pessoa',
        'nome_pessoa',
        'telefone_pessoa',
        'email_pessoa',
        'ativo',
        'id_categoria',
        'codigo_verificacao',
        'cpf_pessoa',
        'data_nasc_pessoa',
        'foto_pessoa'
    ];
    protected $hidden = ['pivot'];
       protected $appends = ['idade'];

    public function getIdadeAttribute()
    {
        return Carbon::parse($this->attributes['data_nasc_pessoa'])->age;
    }
    public function categoriasInteresse()
    {
        return $this->belongsToMany(CategoriaModel::class, 'categoria_pessoa', 'id_pessoa', 'id_categoria');
    }
}
