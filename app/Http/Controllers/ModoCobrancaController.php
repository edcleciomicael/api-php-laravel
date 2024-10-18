<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
//use App\Models\Usuario;
use Illuminate\Support\Facades\DB;

class ModoCobrancaController extends Controller
{
    public function get_modo_cobranca(){

        $query = "
            SELECT cod_modo_cobranca,
                  modo_cobranca
            FROM public.gerais_modo_cobranca
            WHERE cod_modo_cobranca <>
               (SELECT CASE
                           WHEN
                                  (SELECT count(*)
                                   FROM public.gerais_parametros_configuracao
                                   WHERE cod_parametro = 94
                                     AND ativo = TRUE) > 0 THEN 0
                           ELSE 14
                       END)
             AND cod_modo_cobranca <>
               (SELECT CASE
                           WHEN
                                  (SELECT count(*)
                                   FROM public.gerais_parametros_configuracao
                                   WHERE cod_parametro = 95
                                     AND ativo = TRUE) > 0 THEN 0
                           ELSE 13
                       END)
        ";

        $resultado = DB::select($query);

        $cont = 0;
        $cobranca = array();
        foreach ($resultado as $value) {
            $preArray = array();
            $preArray = array(
                'id_modo_cobranca'  => $value->cod_modo_cobranca,
                'modo_cobranca'     => $value->modo_cobranca
            );
            $cobranca[$cont] = $preArray;
            $cont++;
        }

        $result = $cobranca;
        $action = "Consultar modo de cobrança";
        $flag = true;
        return response()->json([$action, $flag, $result]);
    }
}