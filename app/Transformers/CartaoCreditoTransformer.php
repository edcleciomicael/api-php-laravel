<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;

class CartaoCreditoTransformer extends TransformerAbstract
{
    public function transform($financeiroCartaoCredito)
    {
        $formattedUser = [
            'codCartaoCredito'      => $financeiroCartaoCredito->cod_cartao_credito,
            'nomeCartao'            => $financeiroCartaoCredito->nome_cartao,
            'numeroCartao'          => 'terminado em '.\substr($financeiroCartaoCredito->numero_cartao, 12),
            'bandeira'              => $financeiroCartaoCredito->bandeira,
            'credito'               => $financeiroCartaoCredito->credito,
            // 'dataValidade'          => $financeiroCartaoCredito->data_validade,
        ];

        return $formattedUser;
    }
}