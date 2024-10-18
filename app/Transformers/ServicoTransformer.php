<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Models\ContratosServicosContrato;

class ServicoTransformer extends TransformerAbstract
{
    public function transform(ContratosServicosContrato $servico)
    {
        $formatted = [
            'cod_servico_contrato' => $servico->cod_servico_contrato,
            'nome_produto' => $servico->produto->nome_produto,
            'nome_produto_resumido' => $servico->produto->nome_produto_resumido,
            'status_servico' => [
                'cod_status_servico' => $servico->statusServico->cod_status_servico,
                'status_texto' => $servico->statusServico->status_servico
            ]
        ];

        return $formatted;
    }
}
