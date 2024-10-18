<?php

namespace App\Repositories;

use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Model;
use App\Repositories\Contracts\FinanceiroCartaoCreditoTransacaoRepository;

class EloquentFinanceiroCartaoCreditoTransacaoRepository extends AbstractEloquentRepository implements FinanceiroCartaoCreditoTransacaoRepository
{
    public function createRow($codBoleto, $valorBoleto, $codCartao){
        $notNew = $this->findOneBy(array("cod_cartao_credito" => $codCartao));

        $newEntry = $this->save(
            array(
                "cod_boleto" => $codBoleto,
                "tipo_conta" => 'sale',
                "data_pagamento" => date("Y-m-d H:i:s"),
                "valor_boleto" => $valorBoleto,
                "numero_parcelas" => 1,
                "tipo_pagamento" => $notNew ? "existingCard" : 'newCard',
                "cod_cartao_credito" => $codCartao,
                "origem" => 3
            )
        );

        return $newEntry;
    }

    public function checkTransaction($codBoleto){
        return $this->model
                    ->whereRaw("cod_boleto = ".$codBoleto." AND  (status_transacao in ('aguardando_confirmacao', 'notSend', 'authorized', '') or status_transacao is null)")->get();
    }
}