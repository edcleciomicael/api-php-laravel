<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Model;
use App\Repositories\Contracts\FinanceiroFeriadosRepository;

class EloquentFinanceiroFeriadosRepository extends AbstractEloquentRepository implements FinanceiroFeriadosRepository
{
    public function validadeDiasUteis($dataInicial, $prazo){
        $feriados = $this->findBy(['ativo' => true]);
        $feriados_datas = json_decode(json_encode($feriados), true)['data'];

        $data = substr($dataInicial, 0, 10);
        $contador_dias = 0;
        $data_atual = new \DateTime($data);
        while($contador_dias < $prazo){

            if(in_array($data_atual->format('Y-m-d'), $feriados_datas) || $data_atual->format('N') == 6 || $data_atual->format('N') == 0){
                // print_r("expression");
            }else{

                $contador_dias = $contador_dias + 1;
            }

            $data_atual->modify('+1 days');

        }
        // print_r($data_atual->format('Y-m-d'));

        if($data_atual->format('Y-m-d') > date('Y-m-d')){
            return false;
        }

        return true;
    }
}