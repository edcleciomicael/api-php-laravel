<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
//use App\Models\Usuario;
use Illuminate\Support\Facades\DB;

class DiasVencimentoController extends Controller
{
    public function get_dias_vencimento(){

        $query = "
            SELECT array_agg(DISTINCT dia) AS dias_vencimento
            FROM public.financeiro_dias_vencimento
            WHERE habilitado IS TRUE
        ";

        $resultado = DB::select($query);

        $cont = 0;
        $dias = array();
        foreach ($resultado as $value) {
            $preArray = array();
            $preArray = array(
                'dia'  => $value->dias_vencimento
            );
            $dias[$cont] = $preArray;
            $cont++;
        }

        $result = $resultado;
        $action = "Consultar dias de vencimento.";
        $flag = true;
        return response()->json([$action, $flag, $result]);
    }
}