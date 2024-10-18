<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
//use App\Models\Usuario;
use Illuminate\Support\Facades\DB;

class ConsultaUsuariosController extends Controller
{
    public function consulta_usuarios(){
        $action='Busca de usuarios por caixa';
        $flag = false;
        $query=" SELECT 
                            pfv.id_vlan as vlan,
                            pfv.nome as nomeVlan,
                            pcc.username
                            
                        FROM public.provedor_fibra_onu pfo
                            JOIN public.provedor_fibra_onu_mdu_portas pfomp ON pfomp.cod_provedor_fibra_onu = pfo.cod_provedor_fibra_onu
                            JOIN public.provedor_configuracao_cliente pcc ON pcc.cod_configuracao_cliente = pfomp.cod_configuracao_cliente
                            JOIN public.provedor_fibra_vlan pfv ON pfv.cod_provedor_fibra_vlan=pfomp.cod_provedor_fibra_vlan
                        ORDER BY id_vlan
        ";
        $result = DB::select($query);

        return response()->json([$action, $flag, $result]);

    }
}