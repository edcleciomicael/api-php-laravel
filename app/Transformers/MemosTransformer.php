<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Models\ProvedorSuporteMemo;

class MemosTransformer extends TransformerAbstract
{
    public function transform(ProvedorSuporteMemo $item)
    {
        $formatted = [
            'cod_memo' => $item->cod_memo,
            'mostra_na_central' => $item->mostra_central_os,
            'mensagem' => $item->tx_mensagem,
            'data' => $item->dt_memo,
            'servico_realizado' => $item->servico_realizado,
            'viabilidade' => $item->viabilidade,
        ];

        return $formatted;
    }
}