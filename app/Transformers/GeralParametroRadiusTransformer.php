<?php

namespace App\Transformers;

use App\Models\GeralParametroRadius;
use League\Fractal\TransformerAbstract;

class GeralParametroRadiusTransformer extends TransformerAbstract
{
    public function transform(GeralParametroRadius $parametro)
    {
        $formattedData = [
            'cod_parametro'     => $parametro->cod_parametro,
            'parametro'         => $parametro->parametro,
            'ativo'             => $parametro->ativo,
            'valor'             => $parametro->valor,
            'combo_box'        => $parametro->combo_box,
        ];

        return $formattedData;
    }
}