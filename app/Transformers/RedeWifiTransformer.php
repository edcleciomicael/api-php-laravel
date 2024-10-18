<?php

namespace App\Transformers;

use App\Models\ProvedorFibraONUWifi;
use League\Fractal\TransformerAbstract;

class RedeWifiTransformer extends TransformerAbstract
{
    public function transform(ProvedorFibraONUWifi $redeWifi)
    {
        $formatted = [
            'cod_provedor_fibra_onu_wifi' => $redeWifi->cod_provedor_fibra_onu_wifi,
            'cod_configuracao_cliente' => $redeWifi->cod_configuracao_cliente,
            'ssid_24g' => $redeWifi->ssid_24g,
            'ssid_5g' => $redeWifi->ssid_5g,
            'pass_24g' => $redeWifi->pass_24g,
            'pass_5g' => $redeWifi->pass_5g,
            'last_sync' => $redeWifi->last_sync,
            'ativa_central_24g' => $redeWifi->ativa_central_24g,
            'ativa_central_5g' => $redeWifi->ativa_central_5g
        ];

        return $formatted;
    }
}