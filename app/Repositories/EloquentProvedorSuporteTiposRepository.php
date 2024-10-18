<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Model;
use App\Repositories\Contracts\ProvedorSuporteTiposRepository;

class EloquentProvedorSuporteTiposRepository extends AbstractEloquentRepository implements ProvedorSuporteTiposRepository
{
    public function findOne($id)
    {
        return $this->findOneBy(['cod_suporte_tipo' => $id]);
    }

    public function prazoMaximoEmDias($id){
        $tipo_suporte = $this->findOne($id);

        if($tipo_suporte->prazo_max == null || $tipo_suporte->prazo_max == '00:00:00'){
            $date = $tipo_suporte->prazo;
            $date = explode(":", $date);
            $horas = $date[0];
            $prazo = round($horas/24);
        }else{
            $date = $tipo_suporte->prazo_max;
            $date = explode(":", $date);
            $horas = $date[0];
            $prazo = round($horas/24);
        }

        return $prazo;
    }

    public function tipoPrazoDias($id){
        $tipo_suporte = $this->findOne($id);

        if($tipo_suporte->prazo_min != null || $tipo_suporte->prazo_min != '00:00:00'){
            $date = $tipo_suporte->prazo_min;
            $date = explode(":", $date);
            $horas = (int)$date[0];
            $prazo_min = round($horas/24);
        }
        $tipo_suporte->prazo_min = $prazo_min; 

        if($tipo_suporte->prazo_max != null || $tipo_suporte->prazo_max != '00:00:00'){
            $date = $tipo_suporte->prazo_max;
            $date = explode(":", $date);
            $horas = (int)$date[0];
            $prazo_max = round($horas/24);
        }
        $tipo_suporte->prazo_max = $prazo_max; 

        if($tipo_suporte->prazo != null || $tipo_suporte->prazo != '00:00:00'){
            $date = $tipo_suporte->prazo;
            $date = explode(":", $date);
            $horas = $date[0];
            $prazo = round($horas/24);
        }
        $tipo_suporte->prazo = $prazo;

        return $tipo_suporte;
    }
}