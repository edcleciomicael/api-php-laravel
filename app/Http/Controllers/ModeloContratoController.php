<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
//use App\Models\Usuario;
use Illuminate\Support\Facades\DB;

class ModeloContratoController extends Controller
{

    public function get_modelo_contrato(Request $request){

        if(!isset($data['codigo_unidade']) || (isset($data['codigo_unidade']) && !is_null($data['codigo_unidade']))){
            $query = "
                SELECT
                        cod_contrato,
                        nome_contrato,
                        vigencia
                    FROM public.contratos_contrato 
                    WHERE cod_unidade = ?
                        AND ativo IS TRUE
                        AND utilizavel IS TRUE
            ";

            $resultado = DB::select($query, [$request->cod_unidade]);

            //return $resultado;

            if(!empty($resultado)){
                $cont = 0;
                $contratos = array();
                foreach ($resultado as $value) {
                    $preArray = array();
                    $preArray = array(
                        'codigo_modelo_contrato'    => $value->cod_contrato,
                        'modelo_contrato'           => $value->nome_contrato,
                        'vigencia_contratual'       => $value->vigencia
                    );
                    $contratos[$cont] = $preArray;
                    $cont++;
                }

                $result = $contratos;
                $action = "Consultar modelo de contrato";
                $flag = true;
                return response()->json([$action, $flag, $result]);
            }

            $result = "Modelo de contrato não encontrato.";
            $action = "Consultar modelo de contrato";
            $flag = false;
            return response()->json([$action, $flag, $result]);
        }
        else {

            $result = "Unidade não definida.";
            $action = "Consultar modelo de contrato";
            $flag = false;
            return response()->json([$action, $flag, $result]);
        }
        
    }
}
