<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Models\ProvedorSuporteChamado;

class SuportesChamadoTransformer extends TransformerAbstract
{
    public function transform(ProvedorSuporteChamado $item)
    {
        $etapa = null;
        if ($provedorSuporteChamadoEtapas = $item->provedorSuporteChamadoEtapas->first()) {
            $etapa = $provedorSuporteChamadoEtapas->etapasToTransforme();
        }

        $perguntas = [];
        if($item->provedorSuporteAvaliacaoScriptsTipos ? count($item->provedorSuporteAvaliacaoScriptsTipos->provedorSuporteAvaliacaoScriptsPerguntas) > 0 : false){
            foreach ($item->provedorSuporteAvaliacaoScriptsTipos->provedorSuporteAvaliacaoScriptsPerguntas as $pergunta) {
                // print_r("AAAAAAAAAAAAAAAAAAAAAAAAAAAAAA");
                // \var_dump($pergunta);
                // \var_dump($pergunta->provedorSuporteAvaliacaoPerguntas);
                array_push($perguntas, $pergunta->provedorSuporteAvaliacaoPerguntas);
            }
            // exit;
            // $perguntas = $item->provedorSuporteAvaliacaoScriptsTipos->provedorSuporteAvaliacaoScriptsPerguntas;
        }

        $formatted = [
            'cod_chamado' => $item->cod_chamado,
            'agendado' => $item->provedorSuporteAgenda ? $item->provedorSuporteAgenda->agendadoToTransforme() : null,
            'cliente' => $item->pessoa ? $item->pessoa->nome_pessoa : null,
            'tipo' => $item->provedorSuporteTipos ? $item->provedorSuporteTipos->tx_tipo : null,
            'avaliacao' => [
                "possui" => $item->provedorSuporteAvaliacaoScriptsTipos ? true : false,
                "cod_avaliacao_script" => $item->provedorSuporteAvaliacaoScriptsTipos ? $item->provedorSuporteAvaliacaoScriptsTipos->cod_avaliacao_script : false,
                "perguntas" => $perguntas,
            ],
            'avaliacao_feita' => $item->provedorSuporteAvaliacaoChamado ? true : false,
            'etapa' => $etapa,
            'concluido' => $item->concluidoToTransforme(),
            'observacao' => $item->tx_chamado == '' ? 'Sem observação.' : $item->tx_chamado,
        ];

        /*
        case when psa.cod_chamado::text = '' then '-' else to_char(psa.data::date,'DD/MM/YYYY') || ' - ' || to_char(psa.hora_inicio,'HH24:MI:SS') end as dia_agendado,
        cpc.cod_pessoa_contrato,
        cpc.cod_centro,
        p.nome_pessoa, 
        u.nome_completo, 
        case when tx_tipo is null or tx_tipo = '' then 'SEM CLASSIFICAÇÃO' else tx_tipo end as tx_tipo,
        ( select case when psce.posicao = 0 then pse.nome_etapa 
                else ( 
                    select pse1.nome_etapa
                    from provedor_suporte_chamado_etapas psce1
                    join provedor_suporte_etapas pse1 on pse1.cod_etapa = psce1.cod_etapa
                    where psce1.cod_chamado = psc.cod_chamado and psce1.finalizado is false
                    order by psce1.posicao asc, psce1.cod_chamado_etapa asc
                    limit 1
                    ) end
                from provedor_suporte_chamado_etapas psce
                join provedor_suporte_etapas pse on pse.cod_etapa = psce.cod_etapa
                where psce.cod_chamado = psc.cod_chamado and psce.finalizado is false
                order by psce.posicao asc, psce.cod_chamado_etapa asc
                limit 1
        ) as etapa,                
        coalesce(
            trim(
            (select  p.nome_pessoa from provedor_suporte_tecnicos pst                  

                LEFT JOIN usuario u ON u.cod_usuario = pst.cod_usuario
                LEFT JOIN pessoa p ON p.cod_pessoa = u.cod_pessoa 
                where p.cod_unidade = {$cod_unidade} AND p.ativo IS TRUE AND pst.cod_tecnico = psc.cod_tecnico

            )
            ),'-'
        ) as nome_do_tecnico,
        case when dt_prazo < now() then 
            to_char((now()::timestamp - dt_prazo::timestamp), 'DD') || ' Dias ' || to_char((now()::timestamp - dt_prazo::timestamp), 'HH24:MI:SS') 
        else 
            '' 
        end as prazo_expirado,
        */

        return $formatted;
    }
}