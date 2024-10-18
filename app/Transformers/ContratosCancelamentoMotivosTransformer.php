<?php

namespace App\Transformers;

use App\Models\ContratosCancelamentoMotivos;
use League\Fractal\TransformerAbstract;

class ContratosCancelamentoMotivosTransformer extends TransformerAbstract
{
    public function transform(ContratosCancelamentoMotivos $contratosCancelamentoMotivos)
    {
        $formattedUser = [
            'codCancelamentoMotivo' => $contratosCancelamentoMotivos->cod_cancelamento_motivo_central_assinante,
            'motivo' => $contratosCancelamentoMotivos->motivo,
            'ativo' => $contratosCancelamentoMotivos->ativo,
            'permiteObs' => $contratosCancelamentoMotivos->permite_obs,
        ];

        return $formattedUser;
    }
}