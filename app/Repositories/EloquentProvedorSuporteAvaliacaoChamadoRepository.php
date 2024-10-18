<?php

namespace App\Repositories;

use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Model;
use App\Repositories\Contracts\ProvedorSuporteAvaliacaoChamadoRepository;

class EloquentProvedorSuporteAvaliacaoChamadoRepository extends AbstractEloquentRepository implements ProvedorSuporteAvaliacaoChamadoRepository
{
    public function createRow($row){
        // $newEntry = "aaa";
        $newEntry = $this->save(
            array(
                "cod_chamado"           => $row->codChamado,
                "cod_avaliacao_script"  => $row->codAvaliacaoScript,
                "data_avaliacao"        => date("Y-m-d H:i:s"),
                "cod_usuario"           => 0,
                "observacao"            => $row->obsGeral,
            )
        );

        return $newEntry;
    }
}