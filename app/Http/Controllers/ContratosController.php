<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
//use App\Models\Usuario;
use Illuminate\Support\Facades\DB;

class ContratosController extends Controller
{
    public function contratos(Request $request)
    {
        // Validação dos tipos de busca.
        // $tipo_busca
        //   0 => cpf_cnpj
        //   1 => telefone (contrato)

        $remove = array("(", ")", " ", "-", "/", ".");
        $searchReplaced = str_replace($remove, "", $request->search);

        if (
            !preg_match('/^[0-9]+$/', $searchReplaced)
            || !preg_match('/^[0-9]+$/', $request->tipo_busca)
        ){
            $result = "Esta rota requer que todos os argumentos contenham apenas números";
            $action = "Contratos do cliente";
            $flag = false;
            return response()->json([$action, $flag, $result]);
        }else if(!in_array($request->tipo_busca, array(0,1))){
            $result = "tipo_busca inválido";
            $action = "Contratos do cliente";
            $flag = false;
            return response()->json([$action, $flag, $result]);
        }else if($request->tipo_busca == 0 && !(strlen((string) $searchReplaced) == 11 || strlen((string) $searchReplaced) == 14)){
            $result = "cpf ou cnpj inválido";
            $action = "Contratos do cliente";
            $flag = false;
            return response()->json([$action, $flag, $result]);
        }else if($request->tipo_busca == 1 && !(strlen((string) $searchReplaced) == 11 || strlen((string) $searchReplaced) == 10)){
            $result = "telefone inválido";
            $action = "Contratos do cliente";
            $flag = false;
            return response()->json([$action, $flag, $result]);
        }

        $tipo_busca_text = 'cpf ou cnpj';
        $where = "replace(replace(replace(p.cpf_cnpj, '.', ''), '-', ''), '/', '') = replace(replace(replace(?, '.', ''), '-', ''), '/', '')";
        if($request->tipo_busca == 1){
            $tipo_busca_text = 'telefone';
            $searchReplaced = "%{$searchReplaced}%";
            $where = "trim(translate(cpc.telefone, '()- ', '')) like ?";
        }

        $query = "SELECT cpc.cod_pessoa_contrato as codigo_contrato,
                            cpc.cod_unidade as codigo_unidade,
                            cpc.data_ativacao as data_contrato
                        FROM public.pessoa p
                        JOIN public.contratos_pessoa_contrato cpc ON cpc.cod_pessoa = p.cod_pessoa
                        WHERE {$where}
                            AND cpc.cod_status_contrato in (1,6,9)";

        $resultado = DB::select($query, [$searchReplaced]);

        if (!empty($resultado)) {
            $contratos = array();
            foreach ($resultado as $value) {
                $contratos[] = array(
                    'codigo_contrato' => $value->codigo_contrato,
                    'codigo_unidade' => $value->codigo_unidade,
                    'data_contrato' => $value->data_contrato,
                );
            }

            $result = $contratos;
            $action = "Contratos do cliente";
            $flag = true;
            return response()->json([$action, $flag, $result]);
        } else {
            $result = "Contrato não cadastrado para esse {$tipo_busca_text}";
            $action = "Contratos do cliente";
            $flag = false;
            return response()->json([$action, $flag, $result]);
        }
    }
}