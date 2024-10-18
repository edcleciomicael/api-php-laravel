<?php

namespace App\Transformers;

use App\Models\FinanceiroLancamentos;
use League\Fractal\TransformerAbstract;

class LancamentoTransformer extends TransformerAbstract
{
    public function transform($financeiroLancamentos)
    {
        // dd($financeiroBoletos);
        $formattedUser = [
            'cod_lancamento'         => $financeiroLancamentos->cod_lancamento,
            'data_vencimento_valido' => $financeiroLancamentos->data_vencimento_valido,
            'descricao'              => $financeiroLancamentos->descricao,
            'valor_unitario'         => $financeiroLancamentos->valor_unitario,
        ];

        return $formattedUser;
    }
}