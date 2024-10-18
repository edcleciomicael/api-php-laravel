<?php

namespace App\Transformers;

use App\Models\ServicosTrocaDePlanoMotivos;
use League\Fractal\TransformerAbstract;

class ServicosTrocaDePlanoMotivosTransformer extends TransformerAbstract
{
    public function transform(ServicosTrocaDePlanoMotivos $servicosTrocaDePlanoMotivos)
    {
        $formattedUser = [
            'codTrocaDePlanoMotivo' => $servicosTrocaDePlanoMotivos->cod_troca_de_plano_motivo_central_assinante,
            'motivo' => $servicosTrocaDePlanoMotivos->motivo,
            'ativo' => $servicosTrocaDePlanoMotivos->ativo,
            'permiteObs' => $servicosTrocaDePlanoMotivos->permite_obs,
        ];

        return $formattedUser;
    }
}