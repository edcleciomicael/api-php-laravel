<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\ProvedorSuporteChamado;
use Illuminate\Database\Eloquent\Model;
use App\Repositories\Contracts\ProvedorSuporteChamadoRepository;
// use App\Events\UserEvents\UserCreatedEvent;

class EloquentProvedorSuporteChamadoRepository extends AbstractEloquentRepository implements ProvedorSuporteChamadoRepository
{

    public function findOne($id)
    {
        return $this->findOneBy(['cod_chamado' => $id]);
    }
    /**
     * @inheritdoc
     */
    public function suportesPorContrato($codPessoaContrato)
	{

        $query = "
            
            INNER JOIN contratos_servicos_contrato csc ON psc.cod_servico_contrato = csc.cod_servico_contrato

            INNER JOIN contratos_pessoa_contrato cpc ON cpc.cod_pessoa_contrato = csc.cod_pessoa_contrato

            LEFT JOIN provedor_suporte_tipos pst ON pst.cod_suporte_tipo = psc.cod_tipo_chamado
            JOIN pessoa p ON p.cod_pessoa = psc.cod_pessoa
            LEFT JOIN usuario u ON u.cod_usuario = psc.cod_tecnico_abertura
            LEFT JOIN provedor_suporte_agenda psa ON psa.cod_chamado = psc.cod_chamado
            LEFT JOIN provedor_suporte_setor pss ON pss.cod_setor = psc.cod_setor
            LEFT JOIN usuario u2 ON u2.cod_usuario = psc.usuario
            LEFT JOIN contratos_instalacao_equipamento cie ON cie.os_instalacao = psc.cod_chamado

            
        ";

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

        return $this->model
        // ->with('usuario')
        ->whereHas('contratosServicosContrato', function ($query) use ($codPessoaContrato) {
            $query->whereHas('contratosPessoaContrato', function ($query) use ($codPessoaContrato) {
                $query->where('cod_pessoa_contrato', $codPessoaContrato);
            });
        })
        ->whereIn("fl_status", [0,1,2,3,4])
        ->orderBy('cod_chamado')
		->get();
	}

    public function suporteTrocaDeEndereco($codServicoContrato){
        $this->model->suporteTrocaDeEndereco($codServicoContrato);
    }

}
