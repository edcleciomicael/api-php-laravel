<?php

namespace App\Transformers;

use App\Models\GeralParametroConfiguracao;
use League\Fractal\TransformerAbstract;

class ParametroTransformer extends TransformerAbstract
{
    public function transform(GeralParametroConfiguracao $parametro)
    {
        $formattedData = [
            'cod_parametro'     => $parametro->cod_parametro,
            'parametro'         => $parametro->parametro,
            'ativo'             => $parametro->ativo,
            'valor'             => $parametro->valor,
            'tipo_conta'        => $parametro->tipo_conta,
        ];

        return $formattedData;
    }
}