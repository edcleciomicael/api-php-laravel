<?php

namespace App\Transformers;

use App\Models\AppCentralAssinanteParametros;
use League\Fractal\TransformerAbstract;

class AppParametroTransformer extends TransformerAbstract
{
    public function transform(AppCentralAssinanteParametros $parametro)
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