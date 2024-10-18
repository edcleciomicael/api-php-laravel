<?php

namespace App\Repositories;

use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Model;
use App\Repositories\Contracts\ProvedorSuporteAvaliacaoChamadoItensRepository;

class EloquentProvedorSuporteAvaliacaoChamadoItensRepository extends AbstractEloquentRepository implements ProvedorSuporteAvaliacaoChamadoItensRepository
{
    public function createRow($resposta, $codAvaliacaoChamado){
        $newEntry = $this->save(
            array(
                "cod_avaliacao_chamado"     => $codAvaliacaoChamado,
                "cod_avaliacao_pergunta"    => $resposta['cod_avaliacao_pergunta'],
                "aprovado"                  => $resposta['like'],
                "observacao"                => $resposta['obs'],
                )
            );
            
        return $newEntry;
    }
}