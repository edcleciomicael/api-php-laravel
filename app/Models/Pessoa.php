<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
// use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Foundation\Auth\User as Authenticatable;
// use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class Pessoa extends Authenticatable
{
    // use HasFactory;
    use HasApiTokens;

    protected $table = 'public.pessoa';
    public $primaryKey = 'cod_pessoa';
    protected $connection = 'pgsql_public';

    protected $fillable = [
        "cod_pessoa",
        "cod_unidade",
        "nome_pessoa",
        "razao_social",
        "cpf_cnpj",
        "telefone",
        "fax",
        "contato",
        "cod_nacionalidade",
        "cod_estado_civil",
        "cod_profissao",
        "data_cadastro",
        "pessoa_juridica",
        "endereco",
        "numero",
        "complemento",
        "cod_bairro_cidade",
        "responsavel_legal",
        "cpf_responsavel_legal",
        "telefone_responsavel_legal",
        "fax_responsavel_legal",
        "celular_responsavel_legal",
        "email_responsavel_legal",
        "rg",
        "orgao_expedidor",
        "inscricao_estadual",
        "inscricao_municipal",
        "data_nascimento",
        "email",
        "site",
        "cep",
        "codigo_ibge",
        "codigo_ibge_verificador",
        "endereco_fatura",
        "numero_fatura",
        "complemento_fatura",
        "cod_bairro_fatura",
        "cep_fatura",
        "contato_fatura",
        "telefone_fatura",
        "email_fatura",
        "ativo",
        "observacao",
        "sexo",
        "nome_pai",
        "nome_mae",
        "referencia",
        "cod_usuario",
        "coordenadas",
        "cod_funcionario",
        "cod_vendedor",
        "usuario",
        "senha",
        "cod_convenio_bancario",
        "cod_centro",
        "cod_franquia",
        "cod_endereco",
        "telefone_2",
        "dados_bancarios",
        "cargo_responsavel_legal",
        "porte_empresa",
        "cod_pessoa_matriz",
        "rg_responsavel_legal",
        "orgao_expedidor_responsavel_legal",
        "notificar_observacao",
        "total_servicos",
        "total_desativados",
        "total_ativos",
        "total_bloqueados",
        "cod_bloco",
        "apt",
        "lat_lng",
        "created_at",
        "updated_at"
    ];

    public function devicesUsers()
    {
        return $this->hasMany(DevicesUsers::class, 'user_id', 'cod_pessoa');
    }
}
