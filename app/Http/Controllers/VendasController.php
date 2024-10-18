<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
//use App\Models\Usuario;
use Illuminate\Support\Facades\DB;

class VendasController extends Controller
{
    public function get_vendas(Request $request){

        $query = "
            SELECT
                fvpc.*
            FROM public.forca_vendas_pre_contato fvpc
            WHERE replace(replace(replace(fvpc.cpf_cnpj, '.', ''), '-', ''), '/', '') = replace(replace(replace(?, '.', ''), '-', ''), '/', '')
                AND etapa < 5
        ";
        $resultado = DB::select($query, [$request->cpf_cnpj_cliente]);

        if(!empty($resultado)){
            $cont = 0;
            $vendas = array();
            foreach ($resultado as $value) {
                $preArray = array();
                $preArray = array(
                    'codigo_venda'          => $value->cod_pre_contato,
                    'codigo_unidade'        => $value->cod_unidade,
                    'nome_completo_cliente' => $value->nome_pessoa,
                    'razao_social_cliente'  => $value->razao_social,
                    'id_centro_custos'      => $value->cod_centro
                );

                $sql = "
                    SELECT
                            fvpvp.cod_produto,
                            pp.nome_produto,
                            fvpvp.valor_unitario
                        FROM public.forca_vendas_pre_venda_proposta fvpvp
                        JOIN public.produtos_produto pp ON pp.cod_produto = fvpvp.cod_produto
                        WHERE fvpvp.cod_pre_contato = ?
                ";
                $resultado2 = DB::select($sql, [$value->cod_pre_contato]);

                if(!empty($resultado2)){
                    $cont2 = 0;
                    $propostas = array();
                    foreach ($resultado2 as $value2) {
                        $preArray2 = array();
                        $preArray2 = array(
                            'id_plano'      => $value2->cod_produto,
                            'nome_plano'    => $value2->nome_produto,
                            'valor_plano'   => $value2->valor_unitario
                        );
                        $propostas[$cont2] = $preArray2;
                        $cont2++;
                    }
                    $preArray['propostas'] = $propostas;
                }

                $vendas[$cont] = $preArray;
                $cont++;
            }

            $result = $vendas;
            $action = "Consultar vendas em aberto do cliente";
            $flag = true;
            return response()->json([$action, $flag, $result]);
        }

        $result = "Venda não encontrada para esse cpf/cnpj.";
        $action = "Consultar vendas em aberto do cpf/cnpj.";
        $flag = false;
        return response()->json([$action, $flag, $result]);

    }
}