<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
//use App\Models\Usuario;
use Illuminate\Support\Facades\DB;

class ConvenioBancarioController extends Controller
{
    public function get_convenio_bancario(Request $request){
        if(!isset($data['codigo_unidade']) || (isset($data['codigo_unidade']) && !is_null($data['codigo_unidade']))){
            $query = "
                SELECT 
                        fcb.cod_convenio_bancario,
                        fcb.convenio, 
                        fb.banco
                    FROM public.financeiro_convenio_bancario fcb
                    JOIN public.financeiro_banco fb ON fb.cod_banco = fcb.cod_banco
                    JOIN public.pessoa p ON p.cod_pessoa = fcb.cod_pessoa
                    WHERE (p.cod_unidade || fcb.cod_unidades_disponiveis) @> ARRAY[?]::integer[]
                        AND fcb.ativo IS TRUE
            ";

            $resultado = DB::select($query, [$request->cod_unidade]);

            if(!empty($resultado)){
                $cont = 0;
                $convenio = array();
                foreach ($resultado as $value) {
                    $preArray = array();
                    $preArray = array(
                        'id_convenio_bancario'    => $value->cod_convenio_bancario,
                        'banco_convenio'           => $value->banco
                    );
                    $convenio[$cont] = $preArray;
                    $cont++;
                }

                $result = $convenio;
                $action = "Consultar convênio bancário";
                $flag = true;
                return response()->json([$action, $flag, $result]);
            }

            $result = "Convênio bancário não encontrato.";
            $action = "Consultar convênio bancário";
            $flag = false;
            return response()->json([$action, $flag, $result]);
        }
        else {

            $result = "Unidade não definida.";
            $action = "Consultar convênio bancário";
            $flag = false;
            return response()->json([$action, $flag, $result]);
        }
    }  
}