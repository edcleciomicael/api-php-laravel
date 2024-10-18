<?php

namespace App\Transformers;

use App\Models\FinanceiroBoletos;
use League\Fractal\TransformerAbstract;

class BoletoTransformer extends TransformerAbstract
{
    public function transform($financeiroBoletos)
    {
        // dd($financeiroBoletos);
        $formattedUser = [
            'cod_boleto'            => $financeiroBoletos->cod_boleto,
            'valor_total'           => $financeiroBoletos->valor_total,
            'data_vencimento'       => $financeiroBoletos->data_vencimento,
            'cod_status_pagamento'  => $financeiroBoletos->cod_status_pagamento,
            'data_pagamento'        => $financeiroBoletos->data_pagamento,
            'valor_pagamento'       => $financeiroBoletos->valor_pagamento,
            'cod_nota'              => $financeiroBoletos->cod_nota,
            'cod_nota_debito'       => $financeiroBoletos->cod_nota_debito,
            'cod_nota_municipal'    => $financeiroBoletos->cod_nota_municipal,
            'registrado'            => $financeiroBoletos->registrado,
            'avulso'                => $financeiroBoletos->avulso,
            'email_pagseguro'       => $financeiroBoletos->email_pagseguro,
            'nome_produto'          => $financeiroBoletos->nome_produto,
            'atraso'                => $financeiroBoletos->atraso,
        ];

        return $formattedUser;
    }
}