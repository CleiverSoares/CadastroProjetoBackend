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
        'foto_pessoa',
        'observacoes_pessoa',
        'alguem_trabalha',
        'data_entrada_projeto',
        'escolaridade',
        'qtd_pessoas_na_casa',
        'telefone_emergencia',
        'deficiencia_tem_deficiencia',
        'deficiencia_qual_deficiencia',
        'medicamento_tem_alergia',
        'medicamento_qual_medicamento_tem_alergia',  
    ];

    protected $hidden = ['pivot'];
    protected $appends = ['idade'];

    /**
     * Accessor para calcular a idade.
     */
    public function getIdadeAttribute()
    {
        return Carbon::parse($this->attributes['data_nasc_pessoa'])->age;
    }

    /**
     * Relacionamento com categorias de interesse.
     */
    public function categoriasInteresse()
    {
        return $this->belongsToMany(CategoriaModel::class, 'categoria_pessoa', 'id_pessoa', 'id_categoria');
    }

    /**
     * Relacionamento com o endereço da pessoa (na tabela "enderecos").
     */
    public function endereco()
    {
        return $this->hasOne(EnderecoModel::class, 'id_pessoa', 'id_pessoa');
    }

    /**
     * A pessoa tem um campo de deficiência (definido diretamente na tabela pessoa).
     */
    public function getDeficienciaAttribute()
    {
        return [
            'tem_deficiencia' => $this->attributes['deficiencia_tem_deficiencia'],
            'qual_deficiencia' => $this->attributes['deficiencia_qual_deficiencia']
        ];
    }

    /**
     * A pessoa tem um campo de medicamento (definido diretamente na tabela pessoa).
     */
    public function getMedicamentoAttribute()
    {
        return [
            'tem_alergia' => $this->attributes['medicamento_tem_alergia'],
            'qual_medicamento_tem_alergia' => $this->attributes['medicamento_qual_medicamento_tem_alergia']
        ];
    }
}
