<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;

class OnuInfoTransformer extends TransformerAbstract
{
    public function transform($onuInfo)
    {
        $status = null;
        if(isset($onuInfo->Status) || isset($onuInfo->STATUS)){
            if(isset($onuInfo->ONU_INFO->Status)){
                $status = $onuInfo->ONU_INFO->Status;
                if($status === 'Operacional'){
                    $status = '1';
                }
            }else{
                $status = isset($onuInfo->Status) ? $onuInfo->Status : $onuInfo->STATUS;
            }
        }

        $RxPower = null;
        if(isset($onuInfo->Rx) || isset($onuInfo->RxPower) || isset($onuInfo->ONU_INFO->RxPower)){
            if(isset($onuInfo->ONU_INFO->RxPower)){
                $RxPower = $onuInfo->ONU_INFO->RxPower;
            }else{
                $RxPower = isset($onuInfo->RxPower) ? $onuInfo->RxPower : $onuInfo->Rx;
            }
            if((float)$RxPower >= -10){
                $RxPower = 'DISASTER';
            }else if((float)$RxPower >= -16 && (float)$RxPower < -10){
                $RxPower = 'HIGH';
            }else if((float)$RxPower > -22 && (float)$RxPower < -16){
                $RxPower = 'AVERAGE';
            }else if((float)$RxPower > -26 && (float)$RxPower <= -22){
                $RxPower = 'HIGH';
            }else{
                $RxPower = 'DISASTER';
            }
        }

        $formattedData = [
            'cod_provedor_fabricante_olt' => $onuInfo->cod_provedor_fabricante_olt,
            // 'fabricante'    => $onuInfo->fabricante,
            'fabricante'    => '',
            'STATUS' => $status,
            'RxPower' => $RxPower,
            // 'RxPowerR' => $RxPowerR,
        ];

        return $formattedData;
    }
}