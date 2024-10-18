<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Usuario extends Model
{
    use HasFactory;

    protected $table = 'public.usuario';
    public $primaryKey = 'cod_usuario';
    protected $connection = 'pgsql_public';

    protected $fillable = [
        "login",
        "senha",
        "nome_completo",
        "email",
        "periodicidade",
        "ativo",
        "habilitado",
        "cod_pessoa",
        "menu_reduzido",
        "ativa_parametros",
        "ips",
        "segunda",
        "terca",
        "quarta",
        "quinta",
        "sexta",
        "sabado",
        "domingo",
        "time_segunda",
        "time_terca",
        "time_quarta",
        "time_quinta",
        "time_sexta",
        "time_sabado",
        "time_domingo",
        "hora_inicio",
        "hora_fim",
        "cod_unidade",
        "lingua",
        "master",
        "acessa_rb",
        "group_mkt",
        "senha_rb",
        "habilita_suporte",
        "api",
        "suporte_aberto"
    ];

    protected $hidden = [
        'senha',
    ];

    public function pessoa()
    {
        return $this->hasOne(Pessoa::class, 'cod_pessoa', 'cod_pessoa');
    }
}
