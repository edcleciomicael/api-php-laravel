<?php

namespace App\Transformers;

use App\Models\SuporteParametrosConfiguracao;
use League\Fractal\TransformerAbstract;

class SuporteParametrosTransformer extends TransformerAbstract
{
    public function transform(SuporteParametrosConfiguracao $parametro)
    {
        $formattedData = [
            'cod_parametro'     => $parametro->cod_suporte_parametro,
            'parametro'         => $parametro->parametro,
            'ativo'             => $parametro->ativo,
            'valor'             => $parametro->valor,
        ];

        return $formattedData;
    }
}