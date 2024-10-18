<?php

namespace App\Repositories;

use App\Models\FinanceiroBoletos;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Model;
use App\Repositories\Contracts\FinanceiroBoletosRepository;
use Illuminate\Support\Facades\DB;
// use App\Events\UserEvents\UserCreatedEvent;

class EloquentFinanceiroBoletosRepository extends AbstractEloquentRepository implements FinanceiroBoletosRepository
{
    public function chamaBoletosPorContrato($codPessoaContrato = 0, $codPessoa)
    {
        // $sql = "
        //     SELECT
        //         cod_pessoa
        //     FROM public.contratos_pessoa_contrato
        //     WHERE cod_pessoa_contrato = :cod_pessoa_contrato
        // ";

        // $codPessoa = DB::connection('pgsql_public')
        // ->select($sql, ['cod_pessoa_contrato' => $codPessoaContrato])[0]->cod_pessoa;

        $sql = "
            SELECT
                case when exists(SELECT * FROM app_central_assinante_parametros WHERE cod_parametro = 27) is true
                    THEN
                        CASE WHEN (SELECT ativo FROM app_central_assinante_parametros WHERE cod_parametro = 27) is true
                            THEN (SELECT valor FROM app_central_assinante_parametros WHERE cod_parametro = 27)
                            ELSE '0'
                        END
                    ELSE '0'
                END as valor
            FROM app_central_assinante_parametros
            LIMIT 1
        ";
        $diasAtraso = DB::connection('pgsql_public')->select($sql)[0]->valor;

        $sql = "
            SELECT
                    case when exists(SELECT null FROM app_central_assinante_parametros WHERE cod_parametro = 32) is true
                        THEN (SELECT ativo FROM app_central_assinante_parametros WHERE cod_parametro = 32)
                        ELSE false
                    END as ativo
                FROM app_central_assinante_parametros
            LIMIT 1
        ";

        $consultaBoletos = DB::connection('pgsql_public')->select($sql)[0]->ativo;

        if ($consultaBoletos) {
            $sql = "
            SELECT
                *
            FROM 
            (
                SELECT DISTINCT
	CASE
            WHEN fb.novo_vencimento IS NULL THEN
            CASE
                WHEN date(now()) > (( SELECT
                        CASE
                            WHEN contratos_configuracoes.ativo = true THEN dia_util(dia_util(fb.data_vencimento))
                            ELSE fb.data_vencimento
                        END AS data_vencimento
                   FROM contratos_configuracoes
                  WHERE contratos_configuracoes.cod_configuracao = 1)) THEN to_char(date(now())::timestamp with time zone, 'DD/MM/YYYY'::text)
                ELSE to_char((( SELECT
                        CASE
                            WHEN contratos_configuracoes.ativo = true THEN dia_util(dia_util(fb.data_vencimento))
                            ELSE fb.data_vencimento
                        END AS data_vencimento
                   FROM contratos_configuracoes
                  WHERE contratos_configuracoes.cod_configuracao = 1))::timestamp with time zone, 'DD/MM/YYYY'::text)
            END
            ELSE to_char(fb.novo_vencimento::timestamp with time zone, 'DD/MM/YYYY'::text)
        END AS data_vencimento_86,
                    case when date(now()) - fb.data_vencimento::date > 0 then
                        date(now()) - fb.data_vencimento::date
                        else 0 end as dias_atraso,
                    to_char(fb.data_vencimento,'DD/MM/YYYY') as vencimento_original,
                    (
                        (SELECT CASE
            WHEN fb.considera_nao_vencido IS FALSE AND date(now()) > (( SELECT
                    CASE
                        WHEN contratos_configuracoes.ativo = true THEN dia_util(dia_util(fb.data_vencimento))
                        ELSE fb.data_vencimento
                    END AS data_vencimento
               FROM contratos_configuracoes
              WHERE contratos_configuracoes.cod_configuracao = 1)) THEN fb.valor_face +
            CASE
                WHEN fcb.multa_em_porcentagem IS FALSE THEN round(fcb.percentual_multa, 2)
                ELSE fb.valor_face * round(fcb.percentual_multa / 100::numeric, 2)
            END + round((date(now()) - dia_util(fb.data_vencimento))::numeric * (fb.valor_face * fcb.percentual_juros / 100::numeric) / 30::numeric, 2)
            ELSE fb.valor_face
        END)
                            -
                        coalesce((
                            SELECT 
                                sum(mensagem_desconto.valor_desconto) AS sum
                            FROM mensagem_desconto
                            WHERE mensagem_desconto.cod_boleto = fb.cod_boleto
                        ), '0')
                    ) as valor_total,
                    fb.valor_face as valor_original,
                    fb.cod_boleto,
                    fb.valor_pagamento,
                    fb.data_pagamento,
                    fsl.status_lancamento,
                    fnft.cod_nota as cod_nota,
					fnfdt.cod_nota as cod_nota_debito,
					cod_rps as cod_nota_municipal,
                    CASE
            WHEN date(now()) > (( SELECT
                    CASE
                        WHEN contratos_configuracoes.ativo = true THEN dia_util(dia_util(fb.data_vencimento))
                        ELSE fb.data_vencimento
                    END AS data_vencimento
               FROM contratos_configuracoes
              WHERE contratos_configuracoes.cod_configuracao = 1)) THEN 'SIM'::text
            ELSE 'NAO'::text
        END AS atrasado,
                    fb.data_vencimento,
                    fb.boleto_externo_cod_transacao,
                    fb.boleto_externo_link_pagamento,
                    fb.boleto_externo_sincronizado,
                    fb.boleto_externo_mudanca,
                    fbancos.cod_febraban,
            
                    -- Dados relacionado aos boletos integração
                    ib.cod_integrado_boleto,
                    ib.url as integrado_url,
                    ib.barcode as integrado_barcode,
                    ib.status as integrado_status,
                    ib.carne as integrado_carne,
                    ib.carne_capa as integrado_carne_capa,
                    ib.carne_folhas as integrado_carne_folhas,
                    ib.carne_parcela as integrado_carne_parcela,
                    (
                        select
                            coalesce(nullif(case 
                                when 
                                    fp.email is not null
                                then 
                                    case
                                        when ( (SELECT CASE
            WHEN fb.considera_nao_vencido IS FALSE AND date(now()) > (( SELECT
                    CASE
                        WHEN contratos_configuracoes.ativo = true THEN dia_util(dia_util(fb.data_vencimento))
                        ELSE fb.data_vencimento
                    END AS data_vencimento
               FROM contratos_configuracoes
              WHERE contratos_configuracoes.cod_configuracao = 1)) THEN fb.valor_face +
            CASE
                WHEN fcb.multa_em_porcentagem IS FALSE THEN round(fcb.percentual_multa, 2)
                ELSE fb.valor_face * round(fcb.percentual_multa / 100::numeric, 2)
            END + round((date(now()) - dia_util(fb.data_vencimento))::numeric * (fb.valor_face * fcb.percentual_juros / 100::numeric) / 30::numeric, 2)
            ELSE fb.valor_face
        END) between fcb1.valor_min_permitido and fcb1.valor_max_permitido) is true
                                        then 't'
                                        else 'f'
                                    end
                                else 'f'
                            end, ''), 'f') as ativo
                        from public.financeiro_boletos fb1
                        join public.financeiro_convenio_bancario fcb1 on fcb1.cod_convenio_bancario = fb1.cod_convenio_bancario
                        join public.financeiro_pagseguro fp on fp.cod_pagseguro = fcb1.cod_pagseguro
                        where fb1.cod_boleto = fb.cod_boleto
                    ) as pagseguro,
                    fb.cod_status_pagamento,
                    CASE WHEN fcb.cod_banco = 995
                        THEN true
                        ELSE CASE WHEN
                        (
                            SELECT
                                count(fal.cod_boleto)
                            FROM financeiro_arquivo_lancamento fal
                            WHERE fal.cod_boleto = fb.cod_boleto AND fal.cod_comando = '02'
                        ) > 0 THEN true ELSE false END END as registrado,
                    fb.avulso,
                    fp.email as email_pagseguro,
                    (
                        SELECT 
                            string_agg(nome_produto, ', ')
                        from public.chama_boletos cb1
                        left join public.financeiro_nota_fiscal_telecomunicacoes fnft on fnft.cod_boleto=fb.cod_boleto
                        left join public.contratos_servicos_contrato csc on csc.cod_pessoa_contrato = cpc.cod_pessoa_contrato
                        left join public.produtos_produto pp on pp.cod_produto = csc.cod_produto
                        where cb1.cod_boleto = fb.cod_boleto
                        group by cb1.cod_boleto
                    ) as nome_produto,
                    CASE WHEN now()::date > fb.data_vencimento
                        THEN 1
                        ELSE CASE WHEN dia_util((now() + interval '{$diasAtraso} days')::date)::date > fb.data_vencimento
                            THEN 2
                            ELSE 3
                        END
                    END as atraso
FROM financeiro_boletos fb
JOIN financeiro_cobranca fc ON fc.cod_boleto = fb.cod_boleto
JOIN financeiro_lancamentos fl ON fl.cod_lancamento = fc.cod_lancamento
JOIN contratos_parcelas_lancamento cpl on cpl.cod_lancamento = fl.cod_lancamento
JOIN contratos_parcelas cp on cp.cod_parcela = cpl.cod_parcela
JOIN contratos_servicos_contrato csc on csc.cod_servico_contrato = cp.cod_servico_contrato
JOIN contratos_pessoa_contrato cpc ON cpc.cod_pessoa_contrato = csc.cod_pessoa_contrato
JOIN financeiro_status_lancamento fsl ON fsl.cod_status_lancamento = fb.cod_status_pagamento
LEFT JOIN public.financeiro_nota_fiscal_telecomunicacoes fnft on fnft.cod_boleto=fb.cod_boleto AND fnft.tipo_nota = 'N'
LEFT JOIN public.financeiro_nota_fiscal_debito_telecomunicacoes fnfdt on fnfdt.cod_boleto=fb.cod_boleto AND fnfdt.tipo_nota = 'N'
LEFT JOIN nfse.rps rps on rps.cod_boleto = fb.cod_boleto AND rps.nfse_gerado is true
LEFT JOIN public.financeiro_convenio_bancario fcb on fcb.cod_convenio_bancario = fb.cod_convenio_bancario
LEFT JOIN public.financeiro_pagseguro fp on fp.cod_pagseguro = fcb.cod_pagseguro
LEFT JOIN public.financeiro_banco as fbancos on fbancos.cod_banco = fcb.cod_banco
LEFT JOIN public.integrados_boleto ib ON ib.boleto_logica = fb.cod_boleto AND
(CASE WHEN exists(
	SELECT * FROM public.integrados_gnet_acao iga
	WHERE
	fb.cod_boleto = any (iga.boletos)
	AND iga.deleted_at is null
	AND iga.processando = 'f'
) THEN TRUE ELSE FALSE END) = FALSE
where
	cpc.cod_pessoa_contrato = :cod_pessoa_contrato
	AND fb.cod_status_pagamento in (1, 2, 7, 8, 9)
                AND fb.data_vencimento::date between '1970-01-01'::date 
                        and (
                            select 
                                (case 
                                    when ativo = true 
                                    then date(now()) + cast(valor || 'days' as interval) 
                                    else date(now()) + cast('1500 days' as interval) 
                                end)::date 
                            from gerais_parametros_configuracao 
                            where cod_parametro = 19
                        ) 
                -- AND (cod_rps is not null OR fnfdt.cod_nota is not null or fnft.cod_nota is not null)
                -- and
                -- cb.cod_status_pagamento = 1
				and
                (case 
                            when 
                                (select 
                                    count(*) 
                                from public.gerais_parametros_configuracao 
                                where cod_parametro = 103 
                                and ativo = true 
                                and valor > '0')
                                    > 0 
                            then
                                date(now()) - ( SELECT
                CASE
                    WHEN contratos_configuracoes.ativo = true THEN dia_util(dia_util(fb.data_vencimento))
                    ELSE fb.data_vencimento
                END AS data_vencimento
           FROM contratos_configuracoes
          WHERE contratos_configuracoes.cod_configuracao = 1) <=
                                (select 
                                    valor 
                                from public.gerais_parametros_configuracao 
                                where cod_parametro = 103)::int 
                            else 
                                    1=1 
                            end)
                        and (( SELECT
                CASE
                    WHEN contratos_configuracoes.ativo = true THEN dia_util(dia_util(fb.data_vencimento))
                    ELSE fb.data_vencimento
                END AS data_vencimento
           FROM contratos_configuracoes
          WHERE contratos_configuracoes.cod_configuracao = 1) between '1970-01-01'::date and (select (case when ativo = true then date(now()) + cast(valor || 'days' as interval) else date(now()) + cast('1500 days' as interval) end)::date from public.gerais_parametros_configuracao where cod_parametro = 19))
                        and fb.cod_status_pagamento in (1, 7)
                        and (
                            ( SELECT
                CASE
                    WHEN contratos_configuracoes.ativo = true THEN dia_util(dia_util(fb.data_vencimento))
                    ELSE fb.data_vencimento
                END AS data_vencimento
           FROM contratos_configuracoes
          WHERE contratos_configuracoes.cod_configuracao = 1) between '1970-01-01'::date 
                            and (
                                select 
                                    (case 
                                        when ativo = true 
                                        then date(now()) + cast(valor || 'days' as interval) 
                                        else date(now()) + cast('1500 days' as interval) 
                                    end)::date 
                                from gerais_parametros_configuracao 
                                where cod_parametro = 19
                            ) 
                        )
                
                
            
            UNION
            
            SELECT DISTINCT
            CASE
                    WHEN fb.novo_vencimento IS NULL THEN
                    CASE
                        WHEN date(now()) > (( SELECT
                                CASE
                                    WHEN contratos_configuracoes.ativo = true THEN dia_util(dia_util(fb.data_vencimento))
                                    ELSE fb.data_vencimento
                                END AS data_vencimento
                           FROM contratos_configuracoes
                          WHERE contratos_configuracoes.cod_configuracao = 1)) THEN to_char(date(now())::timestamp with time zone, 'DD/MM/YYYY'::text)
                        ELSE to_char((( SELECT
                                CASE
                                    WHEN contratos_configuracoes.ativo = true THEN dia_util(dia_util(fb.data_vencimento))
                                    ELSE fb.data_vencimento
                                END AS data_vencimento
                           FROM contratos_configuracoes
                          WHERE contratos_configuracoes.cod_configuracao = 1))::timestamp with time zone, 'DD/MM/YYYY'::text)
                    END
                    ELSE to_char(fb.novo_vencimento::timestamp with time zone, 'DD/MM/YYYY'::text)
                END AS data_vencimento_86,
                            case when date(now()) - fb.data_vencimento::date > 0 then
                                date(now()) - fb.data_vencimento::date
                                else 0 end as dias_atraso,
                            to_char(fb.data_vencimento,'DD/MM/YYYY') as vencimento_original,
                            (
                                (SELECT CASE
                    WHEN fb.considera_nao_vencido IS FALSE AND date(now()) > (( SELECT
                            CASE
                                WHEN contratos_configuracoes.ativo = true THEN dia_util(dia_util(fb.data_vencimento))
                                ELSE fb.data_vencimento
                            END AS data_vencimento
                       FROM contratos_configuracoes
                      WHERE contratos_configuracoes.cod_configuracao = 1)) THEN fb.valor_face +
                    CASE
                        WHEN fcb.multa_em_porcentagem IS FALSE THEN round(fcb.percentual_multa, 2)
                        ELSE fb.valor_face * round(fcb.percentual_multa / 100::numeric, 2)
                    END + round((date(now()) - dia_util(fb.data_vencimento))::numeric * (fb.valor_face * fcb.percentual_juros / 100::numeric) / 30::numeric, 2)
                    ELSE fb.valor_face
                END)
                                    -
                                coalesce((
                                    SELECT 
                                        sum(mensagem_desconto.valor_desconto) AS sum
                                    FROM mensagem_desconto
                                    WHERE mensagem_desconto.cod_boleto = fb.cod_boleto
                                ), '0')
                            ) as valor_total,
                            fb.valor_face as valor_original,
                            fb.cod_boleto,
                            fb.valor_pagamento,
                            fb.data_pagamento,
                            fsl.status_lancamento,
                            fnft.cod_nota as cod_nota,
                            fnfdt.cod_nota as cod_nota_debito,
                            cod_rps as cod_nota_municipal,
                            CASE
                    WHEN date(now()) > (( SELECT
                            CASE
                                WHEN contratos_configuracoes.ativo = true THEN dia_util(dia_util(fb.data_vencimento))
                                ELSE fb.data_vencimento
                            END AS data_vencimento
                       FROM contratos_configuracoes
                      WHERE contratos_configuracoes.cod_configuracao = 1)) THEN 'SIM'::text
                    ELSE 'NAO'::text
                END AS atrasado,
                            fb.data_vencimento,
                            fb.boleto_externo_cod_transacao,
                            fb.boleto_externo_link_pagamento,
                            fb.boleto_externo_sincronizado,
                            fb.boleto_externo_mudanca,
                            fbancos.cod_febraban,
                    
                            -- Dados relacionado aos boletos integração
                            ib.cod_integrado_boleto,
                            ib.url as integrado_url,
                            ib.barcode as integrado_barcode,
                            ib.status as integrado_status,
                            ib.carne as integrado_carne,
                            ib.carne_capa as integrado_carne_capa,
                            ib.carne_folhas as integrado_carne_folhas,
                            ib.carne_parcela as integrado_carne_parcela,
                            (
                                select
                                    coalesce(nullif(case 
                                        when 
                                            fp.email is not null
                                        then 
                                            case
                                                when ( (SELECT CASE
                    WHEN fb.considera_nao_vencido IS FALSE AND date(now()) > (( SELECT
                            CASE
                                WHEN contratos_configuracoes.ativo = true THEN dia_util(dia_util(fb.data_vencimento))
                                ELSE fb.data_vencimento
                            END AS data_vencimento
                       FROM contratos_configuracoes
                      WHERE contratos_configuracoes.cod_configuracao = 1)) THEN fb.valor_face +
                    CASE
                        WHEN fcb.multa_em_porcentagem IS FALSE THEN round(fcb.percentual_multa, 2)
                        ELSE fb.valor_face * round(fcb.percentual_multa / 100::numeric, 2)
                    END + round((date(now()) - dia_util(fb.data_vencimento))::numeric * (fb.valor_face * fcb.percentual_juros / 100::numeric) / 30::numeric, 2)
                    ELSE fb.valor_face
                END) between fcb1.valor_min_permitido and fcb1.valor_max_permitido) is true
                                                then 't'
                                                else 'f'
                                            end
                                        else 'f'
                                    end, ''), 'f') as ativo
                                from public.financeiro_boletos fb1
                                join public.financeiro_convenio_bancario fcb1 on fcb1.cod_convenio_bancario = fb1.cod_convenio_bancario
                                join public.financeiro_pagseguro fp on fp.cod_pagseguro = fcb1.cod_pagseguro
                                where fb1.cod_boleto = fb.cod_boleto
                            ) as pagseguro,
                            fb.cod_status_pagamento,
                            CASE WHEN fcb.cod_banco = 995
                                THEN true
                                ELSE CASE WHEN
                                (
                                    SELECT
                                        count(fal.cod_boleto)
                                    FROM financeiro_arquivo_lancamento fal
                                    WHERE fal.cod_boleto = fb.cod_boleto AND fal.cod_comando = '02'
                                ) > 0 THEN true ELSE false END END as registrado,
                            fb.avulso,
                            fp.email as email_pagseguro,
                            (
                                SELECT 
                                    string_agg(nome_produto, ', ')
                                from public.chama_boletos cb1
                                left join public.financeiro_nota_fiscal_telecomunicacoes fnft on fnft.cod_boleto=fb.cod_boleto
                                left join public.contratos_servicos_contrato csc on csc.cod_pessoa_contrato = cpc.cod_pessoa_contrato
                                left join public.produtos_produto pp on pp.cod_produto = csc.cod_produto
                                where cb1.cod_boleto = fb.cod_boleto
                                group by cb1.cod_boleto
                            ) as nome_produto,
                            CASE WHEN now()::date > fb.data_vencimento
                                THEN 1
                                ELSE CASE WHEN dia_util((now() + interval '{$diasAtraso} days')::date)::date > fb.data_vencimento
                                    THEN 2
                                    ELSE 3
                                END
                            END as atraso
        FROM financeiro_boletos fb
        JOIN financeiro_cobranca fc ON fc.cod_boleto = fb.cod_boleto
        JOIN financeiro_lancamentos fl ON fl.cod_lancamento = fc.cod_lancamento
        JOIN contratos_parcelas_lancamento cpl on cpl.cod_lancamento = fl.cod_lancamento
        JOIN contratos_parcelas cp on cp.cod_parcela = cpl.cod_parcela
        JOIN contratos_servicos_contrato csc on csc.cod_servico_contrato = cp.cod_servico_contrato
        JOIN contratos_pessoa_contrato cpc ON cpc.cod_pessoa_contrato = csc.cod_pessoa_contrato
        JOIN financeiro_status_lancamento fsl ON fsl.cod_status_lancamento = fb.cod_status_pagamento
        LEFT JOIN public.financeiro_nota_fiscal_telecomunicacoes fnft on fnft.cod_boleto=fb.cod_boleto AND fnft.tipo_nota = 'N'
        LEFT JOIN public.financeiro_nota_fiscal_debito_telecomunicacoes fnfdt on fnfdt.cod_boleto=fb.cod_boleto AND fnfdt.tipo_nota = 'N'
        LEFT JOIN nfse.rps rps on rps.cod_boleto = fb.cod_boleto AND rps.nfse_gerado is true
        LEFT JOIN public.financeiro_convenio_bancario fcb on fcb.cod_convenio_bancario = fb.cod_convenio_bancario
        LEFT JOIN public.financeiro_pagseguro fp on fp.cod_pagseguro = fcb.cod_pagseguro
        LEFT JOIN public.financeiro_banco as fbancos on fbancos.cod_banco = fcb.cod_banco
        LEFT JOIN public.integrados_boleto ib ON ib.boleto_logica = fb.cod_boleto AND
        (CASE WHEN exists(
            SELECT * FROM public.integrados_gnet_acao iga
            WHERE
            fb.cod_boleto = any (iga.boletos)
            AND iga.deleted_at is null
            AND iga.processando = 'f'
        ) THEN TRUE ELSE FALSE END) = FALSE
        where
            cpc.cod_pessoa = :cod_pessoa
            AND fb.cod_status_pagamento in (1, 2, 7, 8, 9)
                        AND fb.data_vencimento::date between '1970-01-01'::date 
                                and (
                                    select 
                                        (case 
                                            when ativo = true 
                                            then date(now()) + cast(valor || 'days' as interval) 
                                            else date(now()) + cast('1500 days' as interval) 
                                        end)::date 
                                    from gerais_parametros_configuracao 
                                    where cod_parametro = 19
                                ) 
                        -- AND (cod_rps is not null OR fnfdt.cod_nota is not null or fnft.cod_nota is not null)
                        -- and
                        -- cb.cod_status_pagamento = 1
				and
                        (case 
                            when 
                                (select 
                                    count(*) 
                                from public.gerais_parametros_configuracao 
                                where cod_parametro = 103 
                                and ativo = true 
                                and valor > '0')
                                    > 0 
                            then
                                date(now()) - ( SELECT
                CASE
                    WHEN contratos_configuracoes.ativo = true THEN dia_util(dia_util(fb.data_vencimento))
                    ELSE fb.data_vencimento
                END AS data_vencimento
           FROM contratos_configuracoes
          WHERE contratos_configuracoes.cod_configuracao = 1) <=
                                (select 
                                    valor 
                                from public.gerais_parametros_configuracao 
                                where cod_parametro = 103)::int 
                            else 
                                    1=1 
                            end)
                        and (( SELECT
                CASE
                    WHEN contratos_configuracoes.ativo = true THEN dia_util(dia_util(fb.data_vencimento))
                    ELSE fb.data_vencimento
                END AS data_vencimento
           FROM contratos_configuracoes
          WHERE contratos_configuracoes.cod_configuracao = 1) between '1970-01-01'::date and (select (case when ativo = true then date(now()) + cast(valor || 'days' as interval) else date(now()) + cast('1500 days' as interval) end)::date from public.gerais_parametros_configuracao where cod_parametro = 19))
                        and fb.cod_status_pagamento in (1, 7)
                        and (
                            ( SELECT
                CASE
                    WHEN contratos_configuracoes.ativo = true THEN dia_util(dia_util(fb.data_vencimento))
                    ELSE fb.data_vencimento
                END AS data_vencimento
           FROM contratos_configuracoes
          WHERE contratos_configuracoes.cod_configuracao = 1) between '1970-01-01'::date 
                            and (
                                select 
                                    (case 
                                        when ativo = true 
                                        then date(now()) + cast(valor || 'days' as interval) 
                                        else date(now()) + cast('1500 days' as interval) 
                                    end)::date 
                                from gerais_parametros_configuracao 
                                where cod_parametro = 19
                            ) 
                        )
                
        
            ) as tab
            order by vencimento_original::date DESC
        ";
        } else {

            $sql = "
            SELECT cb.data_vencimento_86 as data_vencimento,
           CASE
               WHEN date(now()) - vencimento_original::date > 0 THEN date(now()) - vencimento_original::date
               ELSE 0
           END AS dias_atraso,
           to_char(vencimento_original, 'DD/MM/YYYY') AS vencimento_original,
           cb.valor_total,
           cb.valor_face AS valor_original,
           cb.cod_boleto,
           cb.valor_pagamento,
           cb.data_pagamento,
           cb.status_lancamento,
           fnfdt.cod_nota,
           array_remove(
            (SELECT array_agg(deb.cod_nota)
             FROM financeiro_nota_fiscal_debito_telecomunicacoes deb
             WHERE deb.cod_boleto = cb.cod_boleto
                 AND deb.tipo_nota = 'N' ), NULL) AS cod_nota_debito,
            cod_rps as cod_nota_municipal,
            fb.data_vencimento,
            CASE WHEN fcb.cod_banco = 995
                                THEN true
                                ELSE CASE WHEN
                                (
                                    SELECT
                                        count(fal.cod_boleto)
                                    FROM financeiro_arquivo_lancamento fal
                                    WHERE fal.cod_boleto = cb.cod_boleto AND fal.cod_comando = '02'
                                ) > 0 THEN true ELSE false END END as registrado,
            cb.avulso,
            fp.email as email_pagseguro,
            (
                SELECT 
                    string_agg(nome_produto, ', ')
                from public.chama_boletos cb1
                left join public.financeiro_nota_fiscal_telecomunicacoes fnft on fnft.cod_boleto=cb.cod_boleto
                left join public.contratos_servicos_contrato csc on csc.cod_pessoa_contrato = cb.cod_pessoa_contrato
                left join public.produtos_produto pp on pp.cod_produto = csc.cod_produto
                where cb1.cod_boleto = cb.cod_boleto
                group by cb1.cod_boleto
            ) as nome_produto,
           (CASE
                WHEN
                       (SELECT count(*)
                        FROM financeiro_nota_fiscal_telecomunicacoes
                        WHERE cod_boleto = cb.cod_boleto
                          AND tipo_nota = 'N' ) > 0 THEN TRUE
                WHEN
                       (SELECT count(*)
                        FROM financeiro_nota_fiscal_debito_telecomunicacoes
                        WHERE cod_boleto = cb.cod_boleto
                          AND tipo_nota = 'N' ) > 0 THEN TRUE
                WHEN
                       (SELECT count(*)
                        FROM nfse.rps
                        WHERE cod_boleto = cb.cod_boleto
                          AND nfse_gerado IS TRUE ) > 0 THEN TRUE
                ELSE FALSE
            END) AS possui_nota,
           atrasado,
           fb.boleto_externo_cod_transacao,
           fb.boleto_externo_link_pagamento,
           fb.boleto_externo_sincronizado,
           fb.boleto_externo_mudanca,
           fbancos.cod_febraban,
           ib.cod_integrado_boleto,
           ib.url AS integrado_url,
           ib.barcode AS integrado_barcode,
           ib.status AS integrado_status,
           ib.carne AS integrado_carne,
           ib.carne_capa AS integrado_carne_capa,
           ib.carne_folhas AS integrado_carne_folhas,
           ib.carne_parcela AS integrado_carne_parcela,
           CASE WHEN now()::date > fb.data_vencimento
           THEN 1
           ELSE CASE WHEN dia_util((now() + interval '{$diasAtraso} days')::date)::date > fb.data_vencimento
               THEN 2
               ELSE 3
           END
       END as atraso,

      (SELECT coalesce(nullif(CASE
                                  WHEN fp.email IS NOT NULL THEN CASE
                                                                     WHEN (cb.valor_total BETWEEN fcb1.valor_min_permitido AND fcb1.valor_max_permitido) IS TRUE THEN 't'
                                                                     ELSE 'f'
                                                                 END
                                  ELSE 'f'
                              END, ''), 'f') AS ativo
       FROM financeiro_boletos fb1
       JOIN financeiro_convenio_bancario fcb1 ON fcb1.cod_convenio_bancario = fb1.cod_convenio_bancario
       JOIN financeiro_pagseguro fp ON fp.cod_pagseguro = fcb1.cod_pagseguro
       WHERE fb1.cod_boleto = cb.cod_boleto ) AS pagseguro,
           cb.cod_status_pagamento,
    
      (SELECT count(*)
       FROM financeiro_galaxpay fgp
       WHERE fgp.cod_unidade = cb.cod_unidade
         AND fgp.ativo IS TRUE ) > 0 AS galaxpay,
           cb.valor_desconto
    FROM chama_boletos cb
    LEFT JOIN financeiro_nota_fiscal_telecomunicacoes fnft ON fnft.cod_boleto=cb.cod_boleto
    LEFT JOIN public.financeiro_nota_fiscal_debito_telecomunicacoes fnfdt on fnfdt.cod_boleto=cb.cod_boleto AND fnfdt.tipo_nota = 'N'
    LEFT JOIN nfse.rps rps on rps.cod_boleto = cb.cod_boleto AND rps.nfse_gerado is true
    LEFT JOIN financeiro_boletos AS fb ON fb.cod_boleto = cb.cod_boleto
    LEFT JOIN financeiro_convenio_bancario fcb ON fcb.cod_convenio_bancario = fb.cod_convenio_bancario
    LEFT JOIN financeiro_pagseguro fp on fp.cod_pagseguro = fcb.cod_pagseguro
    LEFT JOIN financeiro_banco AS fbancos ON fbancos.cod_banco = fcb.cod_banco
    LEFT JOIN integrados_boleto ib ON ib.boleto_logica = cb.cod_boleto
    AND (CASE
             WHEN exists
                    (SELECT *
                     FROM integrados_gnet_acao iga
                     WHERE cb.cod_boleto = ANY (iga.boletos)
                       AND iga.deleted_at IS NULL
                       AND iga.processando = 'f' ) THEN TRUE
             ELSE FALSE
         END) = FALSE
    WHERE cb.cod_pessoa_contrato = :cod_pessoa_contrato
      AND cb.cod_status_pagamento = 1
      AND (CASE
               WHEN
                      (SELECT count(*)
                       FROM gerais_parametros_configuracao
                       WHERE cod_parametro = 103
                         AND ativo = TRUE
                         AND valor > '0') > 0 THEN date(now()) - vencimento_original::date <=
                      (SELECT valor
                       FROM gerais_parametros_configuracao
                       WHERE cod_parametro = 103)::int
               ELSE 1=1
           END)
      AND (vencimento_original::date BETWEEN '1970-01-01'::date AND
             (SELECT (CASE
                          WHEN ativo = TRUE THEN date(now()) + cast(valor || 'days' AS interval)
                          ELSE date(now()) + cast('1500 days' AS interval)
                      END)::date
              FROM gerais_parametros_configuracao
              WHERE cod_parametro = 19))
      AND fb.cod_status_pagamento in (1,
                                      7)
    ORDER BY vencimento_original::date
            ";
        }
        // $sql = "
        //     SELECT 
        //         fb.cod_status_pagamento,
        //         fb.cod_boleto,
        //         case when fb.novo_vencimento is not null then fb.novo_vencimento else fb.data_vencimento end as data_vencimento,
        //         fb.data_pagamento,
        //         fb.valor_pagamento,
        //         fb.valor_total
        //     FROM financeiro_boletos as fb
        //     INNER JOIN financeiro_cobranca as fc ON fc.cod_boleto = fb.cod_boleto
        //     INNER JOIN contratos_parcelas_lancamento as cpl ON cpl.cod_lancamento = fc.cod_lancamento
        //     INNER JOIN contratos_parcelas as cp ON cp.cod_parcela = cpl.cod_parcela
        //     INNER JOIN contratos_servicos_contrato as csc ON csc.cod_servico_contrato = cp.cod_servico_contrato
        //     INNER JOIN contratos_pessoa_contrato as cpc ON cpc.cod_pessoa_contrato = csc.cod_pessoa_contrato
        //     WHERE
        //     cpc.cod_pessoa_contrato = :cod_pessoa_contrato
        //     and 
        //     (case 
        //         when 
        //             (select 
        //                 count(*) 
        //             from gerais_parametros_configuracao 
        //             where cod_parametro = 103 
        //             and ativo = true 
        //             and valor > '0')
        //                 > 0 
        //         then
        //             date(now()) - fb.data_vencimento::date <=
        //             (select 
        //                 valor 
        //             from gerais_parametros_configuracao 
        //             where cod_parametro = 103)::int 
        //         else 
        //                 1=1 
        //         end)
        //     and (fb.data_vencimento::date between '1970-01-01'::date and (select (case when ativo = true then date(now()) + cast(valor || 'days' as interval) else date(now()) + cast('1500 days' as interval) end)::date from gerais_parametros_configuracao where cod_parametro = 19))
        // ";
        return DB::connection('pgsql_public')
            ->select($sql, ['cod_pessoa_contrato' => $codPessoaContrato]);
    }

    public function chamaBoletosComNotaPorContrato($codPessoaContrato = 0, $codPessoa)
    {
        // $sq

        $sql = "
            SELECT
                case when exists(SELECT * FROM app_central_assinante_parametros WHERE cod_parametro = 27) is true
                    THEN
                        CASE WHEN (SELECT ativo FROM app_central_assinante_parametros WHERE cod_parametro = 27) is true
                            THEN (SELECT valor FROM app_central_assinante_parametros WHERE cod_parametro = 27)
                            ELSE '0'
                        END
                    ELSE '0'
                END as valor
            FROM app_central_assinante_parametros
            WHERE cod_parametro = 27
        ";
        $diasAtraso = DB::connection('pgsql_public')->select($sql)[0]->valor;

        $sql = "
            SELECT
                *
            FROM 
            (
                SELECT DISTINCT
	CASE
            WHEN fb.novo_vencimento IS NULL THEN
            CASE
                WHEN date(now()) > (( SELECT
                        CASE
                            WHEN contratos_configuracoes.ativo = true THEN dia_util(dia_util(fb.data_vencimento))
                            ELSE fb.data_vencimento
                        END AS data_vencimento
                   FROM contratos_configuracoes
                  WHERE contratos_configuracoes.cod_configuracao = 1)) THEN to_char(date(now())::timestamp with time zone, 'DD/MM/YYYY'::text)
                ELSE to_char((( SELECT
                        CASE
                            WHEN contratos_configuracoes.ativo = true THEN dia_util(dia_util(fb.data_vencimento))
                            ELSE fb.data_vencimento
                        END AS data_vencimento
                   FROM contratos_configuracoes
                  WHERE contratos_configuracoes.cod_configuracao = 1))::timestamp with time zone, 'DD/MM/YYYY'::text)
            END
            ELSE to_char(fb.novo_vencimento::timestamp with time zone, 'DD/MM/YYYY'::text)
        END AS data_vencimento_86,
                    case when date(now()) - fb.data_vencimento::date > 0 then
                        date(now()) - fb.data_vencimento::date
                        else 0 end as dias_atraso,
                    to_char(fb.data_vencimento,'DD/MM/YYYY') as vencimento_original,
                    (
                        (SELECT CASE
            WHEN fb.considera_nao_vencido IS FALSE AND date(now()) > (( SELECT
                    CASE
                        WHEN contratos_configuracoes.ativo = true THEN dia_util(dia_util(fb.data_vencimento))
                        ELSE fb.data_vencimento
                    END AS data_vencimento
               FROM contratos_configuracoes
              WHERE contratos_configuracoes.cod_configuracao = 1)) THEN fb.valor_face +
            CASE
                WHEN fcb.multa_em_porcentagem IS FALSE THEN round(fcb.percentual_multa, 2)
                ELSE fb.valor_face * round(fcb.percentual_multa / 100::numeric, 2)
            END + round((date(now()) - dia_util(fb.data_vencimento))::numeric * (fb.valor_face * fcb.percentual_juros / 100::numeric) / 30::numeric, 2)
            ELSE fb.valor_face
        END)
                            -
                        coalesce((
                            SELECT 
                                sum(mensagem_desconto.valor_desconto) AS sum
                            FROM mensagem_desconto
                            WHERE mensagem_desconto.cod_boleto = fb.cod_boleto
                        ), '0')
                    ) as valor_total,
                    fb.valor_face as valor_original,
                    fb.cod_boleto,
                    fb.valor_pagamento,
                    fb.data_pagamento,
                    fsl.status_lancamento,
                    fnft.cod_nota as cod_nota,
					fnfdt.cod_nota as cod_nota_debito,
					cod_rps as cod_nota_municipal,
                    CASE
            WHEN date(now()) > (( SELECT
                    CASE
                        WHEN contratos_configuracoes.ativo = true THEN dia_util(dia_util(fb.data_vencimento))
                        ELSE fb.data_vencimento
                    END AS data_vencimento
               FROM contratos_configuracoes
              WHERE contratos_configuracoes.cod_configuracao = 1)) THEN 'SIM'::text
            ELSE 'NAO'::text
        END AS atrasado,
                    fb.data_vencimento,
                    fb.boleto_externo_cod_transacao,
                    fb.boleto_externo_link_pagamento,
                    fb.boleto_externo_sincronizado,
                    fb.boleto_externo_mudanca,
                    fbancos.cod_febraban,
            
                    -- Dados relacionado aos boletos integração
                    ib.cod_integrado_boleto,
                    ib.url as integrado_url,
                    ib.barcode as integrado_barcode,
                    ib.status as integrado_status,
                    ib.carne as integrado_carne,
                    ib.carne_capa as integrado_carne_capa,
                    ib.carne_folhas as integrado_carne_folhas,
                    ib.carne_parcela as integrado_carne_parcela,
                    (
                        select
                            coalesce(nullif(case 
                                when 
                                    fp.email is not null
                                then 
                                    case
                                        when ( (SELECT CASE
            WHEN fb.considera_nao_vencido IS FALSE AND date(now()) > (( SELECT
                    CASE
                        WHEN contratos_configuracoes.ativo = true THEN dia_util(dia_util(fb.data_vencimento))
                        ELSE fb.data_vencimento
                    END AS data_vencimento
               FROM contratos_configuracoes
              WHERE contratos_configuracoes.cod_configuracao = 1)) THEN fb.valor_face +
            CASE
                WHEN fcb.multa_em_porcentagem IS FALSE THEN round(fcb.percentual_multa, 2)
                ELSE fb.valor_face * round(fcb.percentual_multa / 100::numeric, 2)
            END + round((date(now()) - dia_util(fb.data_vencimento))::numeric * (fb.valor_face * fcb.percentual_juros / 100::numeric) / 30::numeric, 2)
            ELSE fb.valor_face
        END) between fcb1.valor_min_permitido and fcb1.valor_max_permitido) is true
                                        then 't'
                                        else 'f'
                                    end
                                else 'f'
                            end, ''), 'f') as ativo
                        from public.financeiro_boletos fb1
                        join public.financeiro_convenio_bancario fcb1 on fcb1.cod_convenio_bancario = fb1.cod_convenio_bancario
                        join public.financeiro_pagseguro fp on fp.cod_pagseguro = fcb1.cod_pagseguro
                        where fb1.cod_boleto = fb.cod_boleto
                    ) as pagseguro,
                    fb.cod_status_pagamento,
                    CASE WHEN fcb.cod_banco = 995
                        THEN true
                        ELSE CASE WHEN
                        (
                            SELECT
                                count(fal.cod_boleto)
                            FROM financeiro_arquivo_lancamento fal
                            WHERE fal.cod_boleto = fb.cod_boleto AND fal.cod_comando = '02'
                        ) > 0 THEN true ELSE false END END as registrado,
                    fb.avulso,
                    fp.email as email_pagseguro,
                    (
                        SELECT 
                            string_agg(nome_produto, ', ')
                        from public.chama_boletos cb1
                        left join public.financeiro_nota_fiscal_telecomunicacoes fnft on fnft.cod_boleto=fb.cod_boleto
                        left join public.contratos_servicos_contrato csc on csc.cod_pessoa_contrato = cpc.cod_pessoa_contrato
                        left join public.produtos_produto pp on pp.cod_produto = csc.cod_produto
                        where cb1.cod_boleto = fb.cod_boleto
                        group by cb1.cod_boleto
                    ) as nome_produto,
                    CASE WHEN now()::date > fb.data_vencimento
                        THEN 1
                        ELSE CASE WHEN dia_util((now() + interval '{$diasAtraso} days')::date)::date > fb.data_vencimento
                            THEN 2
                            ELSE 3
                        END
                    END as atraso
FROM financeiro_boletos fb
JOIN financeiro_cobranca fc ON fc.cod_boleto = fb.cod_boleto
JOIN financeiro_lancamentos fl ON fl.cod_lancamento = fc.cod_lancamento
JOIN contratos_parcelas_lancamento cpl on cpl.cod_lancamento = fl.cod_lancamento
JOIN contratos_parcelas cp on cp.cod_parcela = cpl.cod_parcela
JOIN contratos_servicos_contrato csc on csc.cod_servico_contrato = cp.cod_servico_contrato
JOIN contratos_pessoa_contrato cpc ON cpc.cod_pessoa_contrato = csc.cod_pessoa_contrato
JOIN financeiro_status_lancamento fsl ON fsl.cod_status_lancamento = fb.cod_status_pagamento
LEFT JOIN public.financeiro_nota_fiscal_telecomunicacoes fnft on fnft.cod_boleto=fb.cod_boleto AND fnft.tipo_nota = 'N'
LEFT JOIN public.financeiro_nota_fiscal_debito_telecomunicacoes fnfdt on fnfdt.cod_boleto=fb.cod_boleto AND fnfdt.tipo_nota = 'N'
LEFT JOIN nfse.rps rps on rps.cod_boleto = fb.cod_boleto AND rps.nfse_gerado is true
LEFT JOIN public.financeiro_convenio_bancario fcb on fcb.cod_convenio_bancario = fb.cod_convenio_bancario
LEFT JOIN public.financeiro_pagseguro fp on fp.cod_pagseguro = fcb.cod_pagseguro
LEFT JOIN public.financeiro_banco as fbancos on fbancos.cod_banco = fcb.cod_banco
LEFT JOIN public.integrados_boleto ib ON ib.boleto_logica = fb.cod_boleto AND
(CASE WHEN exists(
	SELECT * FROM public.integrados_gnet_acao iga
	WHERE
	fb.cod_boleto = any (iga.boletos)
	AND iga.deleted_at is null
	AND iga.processando = 'f'
) THEN TRUE ELSE FALSE END) = FALSE
where
	cpc.cod_pessoa_contrato = :cod_pessoa_contrato
	AND fb.cod_status_pagamento in (1, 2, 7, 8, 9)
                AND fb.data_vencimento::date between '1970-01-01'::date 
                        and (
                            select 
                                (case 
                                    when ativo = true 
                                    then date(now()) + cast(valor || 'days' as interval) 
                                    else date(now()) + cast('1500 days' as interval) 
                                end)::date 
                            from gerais_parametros_configuracao 
                            where cod_parametro = 19
                        ) 
                -- AND (cod_rps is not null OR fnfdt.cod_nota is not null or fnft.cod_nota is not null)
                -- and
                -- cb.cod_status_pagamento = 1
                
                
                
            
            UNION
            
            SELECT DISTINCT
            CASE
                    WHEN fb.novo_vencimento IS NULL THEN
                    CASE
                        WHEN date(now()) > (( SELECT
                                CASE
                                    WHEN contratos_configuracoes.ativo = true THEN dia_util(dia_util(fb.data_vencimento))
                                    ELSE fb.data_vencimento
                                END AS data_vencimento
                           FROM contratos_configuracoes
                          WHERE contratos_configuracoes.cod_configuracao = 1)) THEN to_char(date(now())::timestamp with time zone, 'DD/MM/YYYY'::text)
                        ELSE to_char((( SELECT
                                CASE
                                    WHEN contratos_configuracoes.ativo = true THEN dia_util(dia_util(fb.data_vencimento))
                                    ELSE fb.data_vencimento
                                END AS data_vencimento
                           FROM contratos_configuracoes
                          WHERE contratos_configuracoes.cod_configuracao = 1))::timestamp with time zone, 'DD/MM/YYYY'::text)
                    END
                    ELSE to_char(fb.novo_vencimento::timestamp with time zone, 'DD/MM/YYYY'::text)
                END AS data_vencimento_86,
                            case when date(now()) - fb.data_vencimento::date > 0 then
                                date(now()) - fb.data_vencimento::date
                                else 0 end as dias_atraso,
                            to_char(fb.data_vencimento,'DD/MM/YYYY') as vencimento_original,
                            (
                                (SELECT CASE
                    WHEN fb.considera_nao_vencido IS FALSE AND date(now()) > (( SELECT
                            CASE
                                WHEN contratos_configuracoes.ativo = true THEN dia_util(dia_util(fb.data_vencimento))
                                ELSE fb.data_vencimento
                            END AS data_vencimento
                       FROM contratos_configuracoes
                      WHERE contratos_configuracoes.cod_configuracao = 1)) THEN fb.valor_face +
                    CASE
                        WHEN fcb.multa_em_porcentagem IS FALSE THEN round(fcb.percentual_multa, 2)
                        ELSE fb.valor_face * round(fcb.percentual_multa / 100::numeric, 2)
                    END + round((date(now()) - dia_util(fb.data_vencimento))::numeric * (fb.valor_face * fcb.percentual_juros / 100::numeric) / 30::numeric, 2)
                    ELSE fb.valor_face
                END)
                                    -
                                coalesce((
                                    SELECT 
                                        sum(mensagem_desconto.valor_desconto) AS sum
                                    FROM mensagem_desconto
                                    WHERE mensagem_desconto.cod_boleto = fb.cod_boleto
                                ), '0')
                            ) as valor_total,
                            fb.valor_face as valor_original,
                            fb.cod_boleto,
                            fb.valor_pagamento,
                            fb.data_pagamento,
                            fsl.status_lancamento,
                            fnft.cod_nota as cod_nota,
                            fnfdt.cod_nota as cod_nota_debito,
                            cod_rps as cod_nota_municipal,
                            CASE
                    WHEN date(now()) > (( SELECT
                            CASE
                                WHEN contratos_configuracoes.ativo = true THEN dia_util(dia_util(fb.data_vencimento))
                                ELSE fb.data_vencimento
                            END AS data_vencimento
                       FROM contratos_configuracoes
                      WHERE contratos_configuracoes.cod_configuracao = 1)) THEN 'SIM'::text
                    ELSE 'NAO'::text
                END AS atrasado,
                            fb.data_vencimento,
                            fb.boleto_externo_cod_transacao,
                            fb.boleto_externo_link_pagamento,
                            fb.boleto_externo_sincronizado,
                            fb.boleto_externo_mudanca,
                            fbancos.cod_febraban,
                    
                            -- Dados relacionado aos boletos integração
                            ib.cod_integrado_boleto,
                            ib.url as integrado_url,
                            ib.barcode as integrado_barcode,
                            ib.status as integrado_status,
                            ib.carne as integrado_carne,
                            ib.carne_capa as integrado_carne_capa,
                            ib.carne_folhas as integrado_carne_folhas,
                            ib.carne_parcela as integrado_carne_parcela,
                            (
                                select
                                    coalesce(nullif(case 
                                        when 
                                            fp.email is not null
                                        then 
                                            case
                                                when ( (SELECT CASE
                    WHEN fb.considera_nao_vencido IS FALSE AND date(now()) > (( SELECT
                            CASE
                                WHEN contratos_configuracoes.ativo = true THEN dia_util(dia_util(fb.data_vencimento))
                                ELSE fb.data_vencimento
                            END AS data_vencimento
                       FROM contratos_configuracoes
                      WHERE contratos_configuracoes.cod_configuracao = 1)) THEN fb.valor_face +
                    CASE
                        WHEN fcb.multa_em_porcentagem IS FALSE THEN round(fcb.percentual_multa, 2)
                        ELSE fb.valor_face * round(fcb.percentual_multa / 100::numeric, 2)
                    END + round((date(now()) - dia_util(fb.data_vencimento))::numeric * (fb.valor_face * fcb.percentual_juros / 100::numeric) / 30::numeric, 2)
                    ELSE fb.valor_face
                END) between fcb1.valor_min_permitido and fcb1.valor_max_permitido) is true
                                                then 't'
                                                else 'f'
                                            end
                                        else 'f'
                                    end, ''), 'f') as ativo
                                from public.financeiro_boletos fb1
                                join public.financeiro_convenio_bancario fcb1 on fcb1.cod_convenio_bancario = fb1.cod_convenio_bancario
                                join public.financeiro_pagseguro fp on fp.cod_pagseguro = fcb1.cod_pagseguro
                                where fb1.cod_boleto = fb.cod_boleto
                            ) as pagseguro,
                            fb.cod_status_pagamento,
                            CASE WHEN fcb.cod_banco = 995
                                THEN true
                                ELSE CASE WHEN
                                (
                                    SELECT
                                        count(fal.cod_boleto)
                                    FROM financeiro_arquivo_lancamento fal
                                    WHERE fal.cod_boleto = fb.cod_boleto AND fal.cod_comando = '02'
                                ) > 0 THEN true ELSE false END END as registrado,
                            fb.avulso,
                            fp.email as email_pagseguro,
                            (
                                SELECT 
                                    string_agg(nome_produto, ', ')
                                from public.chama_boletos cb1
                                left join public.financeiro_nota_fiscal_telecomunicacoes fnft on fnft.cod_boleto=fb.cod_boleto
                                left join public.contratos_servicos_contrato csc on csc.cod_pessoa_contrato = cpc.cod_pessoa_contrato
                                left join public.produtos_produto pp on pp.cod_produto = csc.cod_produto
                                where cb1.cod_boleto = fb.cod_boleto
                                group by cb1.cod_boleto
                            ) as nome_produto,
                            CASE WHEN now()::date > fb.data_vencimento
                                THEN 1
                                ELSE CASE WHEN dia_util((now() + interval '{$diasAtraso} days')::date)::date > fb.data_vencimento
                                    THEN 2
                                    ELSE 3
                                END
                            END as atraso
        FROM financeiro_boletos fb
        JOIN financeiro_cobranca fc ON fc.cod_boleto = fb.cod_boleto
        JOIN financeiro_lancamentos fl ON fl.cod_lancamento = fc.cod_lancamento
        JOIN contratos_parcelas_lancamento cpl on cpl.cod_lancamento = fl.cod_lancamento
        JOIN contratos_parcelas cp on cp.cod_parcela = cpl.cod_parcela
        JOIN contratos_servicos_contrato csc on csc.cod_servico_contrato = cp.cod_servico_contrato
        JOIN contratos_pessoa_contrato cpc ON cpc.cod_pessoa_contrato = csc.cod_pessoa_contrato
        JOIN financeiro_status_lancamento fsl ON fsl.cod_status_lancamento = fb.cod_status_pagamento
        LEFT JOIN public.financeiro_nota_fiscal_telecomunicacoes fnft on fnft.cod_boleto=fb.cod_boleto AND fnft.tipo_nota = 'N'
        LEFT JOIN public.financeiro_nota_fiscal_debito_telecomunicacoes fnfdt on fnfdt.cod_boleto=fb.cod_boleto AND fnfdt.tipo_nota = 'N'
        LEFT JOIN nfse.rps rps on rps.cod_boleto = fb.cod_boleto AND rps.nfse_gerado is true
        LEFT JOIN public.financeiro_convenio_bancario fcb on fcb.cod_convenio_bancario = fb.cod_convenio_bancario
        LEFT JOIN public.financeiro_pagseguro fp on fp.cod_pagseguro = fcb.cod_pagseguro
        LEFT JOIN public.financeiro_banco as fbancos on fbancos.cod_banco = fcb.cod_banco
        LEFT JOIN public.integrados_boleto ib ON ib.boleto_logica = fb.cod_boleto AND
        (CASE WHEN exists(
            SELECT * FROM public.integrados_gnet_acao iga
            WHERE
            fb.cod_boleto = any (iga.boletos)
            AND iga.deleted_at is null
            AND iga.processando = 'f'
        ) THEN TRUE ELSE FALSE END) = FALSE
        where
            cpc.cod_pessoa = :cod_pessoa
            AND fb.cod_status_pagamento in (1, 2, 7, 8, 9)
                        AND fb.data_vencimento::date between '1970-01-01'::date 
                                and (
                                    select 
                                        (case 
                                            when ativo = true 
                                            then date(now()) + cast(valor || 'days' as interval) 
                                            else date(now()) + cast('1500 days' as interval) 
                                        end)::date 
                                    from gerais_parametros_configuracao 
                                    where cod_parametro = 19
                                ) 
                        -- AND (cod_rps is not null OR fnfdt.cod_nota is not null or fnft.cod_nota is not null)
                        -- and
                        -- cb.cod_status_pagamento = 1
                        
                
        
            ) as tab
            order by vencimento_original::date DESC
        ";

        return DB::connection('pgsql_public')
            ->select($sql, ['cod_pessoa_contrato' => $codPessoaContrato, 'cod_pessoa' => $codPessoa]);
    }

    public function valorTotalBoleto($codBoleto)
    {
        $sql = "
            SELECT 
                cb.data_vencimento, 
                case 
                    when 
                        date(now()) - vencimento_original::date > 0 
                    then date(now()) - vencimento_original::date 
                    else 0 
                end as dias_atraso, 
                to_char(vencimento_original,'DD/MM/YYYY') as vencimento_original, 
                (
                    cb.valor_total 
                        -
                    coalesce((
                        SELECT 
                            sum(mensagem_desconto.valor_desconto) AS sum
                        FROM mensagem_desconto
                        WHERE mensagem_desconto.cod_boleto = cb.cod_boleto
                    ), '0')
                ) as valor_total, 
                valor_face as valor_original, 
                cb.cod_boleto, 
                cb.valor_pagamento, 
                cb.data_pagamento, 
                cb.status_lancamento, 
                cod_nota,
                case
                    when
                        nome_produto is null
                    then
                        'Boleto Avulso'
                    else
                        nome_produto
                end as nome_produto
            from chama_boletos cb
            left join financeiro_nota_fiscal_telecomunicacoes fnft on fnft.cod_boleto=cb.cod_boleto
            left join contratos_servicos_contrato csc on csc.cod_pessoa_contrato = cb.cod_pessoa_contrato
            left join produtos_produto pp on pp.cod_produto = csc.cod_produto
            where cb.cod_boleto = :cod_boleto 
            and cb.cod_status_pagamento = 1 
            and (
                vencimento_original::date between '1970-01-01'::date 
                and (
                    select 
                        (case 
                            when ativo = true 
                            then date(now()) + cast(valor || 'days' as interval) 
                            else date(now()) + cast('1500 days' as interval) 
                        end)::date 
                    from gerais_parametros_configuracao 
                    where cod_parametro = 19
                ) 
            )
            --and fixo is true
            --and parcelas = 0
            order by vencimento_original::date
            limit 1
        ";

        return DB::connection('pgsql_public')->select($sql, ['cod_boleto' => $codBoleto]);
    }

    public function verificaAtraso($codPessoaContrato, $codPessoa)
    {

        $sql = "
            SELECT
                valor
            FROM app_central_assinante_parametros
            WHERE cod_parametro = 27
        ";

        $diasAtraso = DB::connection('pgsql_public')->select($sql)[0]->valor;

        // dd($diasAtraso);    

        $sql = "
            SELECT
                CASE WHEN (SELECT
                    count(atraso) FROM (
                        SELECT
                                CASE WHEN now()::date > fb.data_vencimento
                                    THEN 1
                                    ELSE CASE WHEN dia_util((now() + interval '{$diasAtraso} days')::date)::date > fb.data_vencimento
                                        THEN 2
                                        ELSE 3
                                    END
                                END as atraso
                            FROM contratos_pessoa_contrato cpc
                            JOIN contratos_servicos_contrato csc ON csc.cod_pessoa_contrato = cpc.cod_pessoa_contrato
                            JOIN contratos_parcelas cp ON cp.cod_servico_contrato = csc.cod_servico_contrato
                            JOIN contratos_parcelas_lancamento cpl ON cpl.cod_parcela = cp.cod_parcela
                            JOIN financeiro_cobranca fc ON fc.cod_lancamento = cpl.cod_lancamento
                            JOIN financeiro_boletos fb ON fb.cod_boleto = fc.cod_boleto
                            WHERE (cpc.cod_pessoa_contrato = :cod_pessoa_contrato OR (cpc.cod_pessoa = :cod_pessoa AND fb.avulso is true)) AND fb.cod_status_pagamento = 1
                            GROUP BY fb.data_vencimento, cpc.cod_pessoa, fb.avulso
                            HAVING now()::date > fb.data_vencimento
                    ) as tab) > 0 THEN 1
                    ELSE CASE WHEN (SELECT
                    count(atraso) FROM (
                        SELECT
                                CASE WHEN now()::date > fb.data_vencimento
                                    THEN 1
                                    ELSE CASE WHEN dia_util((now() + interval ' {$diasAtraso} days')::date)::date > fb.data_vencimento
                                        THEN 2
                                        ELSE 3
                                    END
                                END as atraso
                            FROM contratos_pessoa_contrato cpc
                            JOIN contratos_servicos_contrato csc ON csc.cod_pessoa_contrato = cpc.cod_pessoa_contrato
                            JOIN contratos_parcelas cp ON cp.cod_servico_contrato = csc.cod_servico_contrato
                            JOIN contratos_parcelas_lancamento cpl ON cpl.cod_parcela = cp.cod_parcela
                            JOIN financeiro_cobranca fc ON fc.cod_lancamento = cpl.cod_lancamento
                            JOIN financeiro_boletos fb ON fb.cod_boleto = fc.cod_boleto
                            WHERE (cpc.cod_pessoa_contrato = :cod_pessoa_contrato OR (cpc.cod_pessoa = :cod_pessoa AND fb.avulso is true)) AND fb.cod_status_pagamento = 1
                            GROUP BY fb.data_vencimento, cpc.cod_pessoa, fb.avulso
                            HAVING dia_util((now()::date + interval '{$diasAtraso} days')::date)::date > fb.data_vencimento
                    ) as tab) > 0 THEN 2 ELSE 3 END END as atraso,
                    (
						SELECT
                            to_char(fb.data_vencimento, 'DD/MM/YYYY')
							FROM contratos_pessoa_contrato cpc
                            JOIN contratos_servicos_contrato csc ON csc.cod_pessoa_contrato = cpc.cod_pessoa_contrato
                            JOIN contratos_parcelas cp ON cp.cod_servico_contrato = csc.cod_servico_contrato
                            JOIN contratos_parcelas_lancamento cpl ON cpl.cod_parcela = cp.cod_parcela
                            JOIN financeiro_cobranca fc ON fc.cod_lancamento = cpl.cod_lancamento
                            JOIN financeiro_boletos fb ON fb.cod_boleto = fc.cod_boleto
						WHERE (cpc.cod_pessoa_contrato = :cod_pessoa_contrato OR (cpc.cod_pessoa = :cod_pessoa AND fb.avulso is true)) AND fb.data_vencimento > now()::date
						ORDER BY fb.data_vencimento
						LIMIT 1
                    ) as data_vencimento,
                    (
                        SELECT
                            cpc.forma_contrato
                        FROM contratos_pessoa_contrato cpc
                        WHERE cpc.cod_pessoa_contrato = :cod_pessoa_contrato
                    ) as forma_contrato,
                    CASE WHEN (
                        SELECT DISTINCT
                            count(DISTINCT fb.cod_boleto)
                        FROM contratos_pessoa_contrato cpc
                        JOIN contratos_servicos_contrato csc ON csc.cod_pessoa_contrato = cpc.cod_pessoa_contrato
                        JOIN contratos_parcelas cp ON cp.cod_servico_contrato = csc.cod_servico_contrato
                        JOIN contratos_parcelas_lancamento cpl ON cpl.cod_parcela = cp.cod_parcela
                        JOIN financeiro_cobranca fc ON fc.cod_lancamento = cpl.cod_lancamento
                        JOIN financeiro_boletos fb ON fb.cod_boleto = fc.cod_boleto
                        WHERE (cpc.cod_pessoa_contrato = :cod_pessoa_contrato OR (cpc.cod_pessoa = :cod_pessoa AND fb.avulso is true)) AND fb.cod_status_pagamento = 1
                        AND now()::date > fb.data_vencimento ) = 1 THEN (
                        SELECT
                            fb.cod_boleto
                        FROM contratos_pessoa_contrato cpc
                        JOIN contratos_servicos_contrato csc ON csc.cod_pessoa_contrato = cpc.cod_pessoa_contrato
                        JOIN contratos_parcelas cp ON cp.cod_servico_contrato = csc.cod_servico_contrato
                        JOIN contratos_parcelas_lancamento cpl ON cpl.cod_parcela = cp.cod_parcela
                        JOIN financeiro_cobranca fc ON fc.cod_lancamento = cpl.cod_lancamento
                        JOIN financeiro_boletos fb ON fb.cod_boleto = fc.cod_boleto
                        WHERE (cpc.cod_pessoa_contrato = :cod_pessoa_contrato OR (cpc.cod_pessoa = :cod_pessoa AND fb.avulso is true)) AND fb.cod_status_pagamento = 1
                        ORDER BY fb.data_vencimento
                        LIMIT 1
                    ) ELSE null END as cod_atrasado,
                    (
                        SELECT
                            fb.cod_boleto
                        FROM contratos_pessoa_contrato cpc
                        JOIN contratos_servicos_contrato csc ON csc.cod_pessoa_contrato = cpc.cod_pessoa_contrato
                        JOIN contratos_parcelas cp ON cp.cod_servico_contrato = csc.cod_servico_contrato
                        JOIN contratos_parcelas_lancamento cpl ON cpl.cod_parcela = cp.cod_parcela
                        JOIN financeiro_cobranca fc ON fc.cod_lancamento = cpl.cod_lancamento
                        JOIN financeiro_boletos fb ON fb.cod_boleto = fc.cod_boleto
                        WHERE (cpc.cod_pessoa_contrato = :cod_pessoa_contrato OR (cpc.cod_pessoa = :cod_pessoa AND fb.avulso is true)) AND fb.cod_status_pagamento = 1
                        ORDER BY fb.data_vencimento
                        LIMIT 1
                    ) as cod_proximo
                    
        ";

        return DB::connection('pgsql_public')
            ->select($sql, ['cod_pessoa_contrato' => $codPessoaContrato, 'cod_pessoa' => $codPessoa])[0];
    }

    public function getCopiaCola($codBoleto = null)
    {
        $sql = "
        select * FROM logica.get_pix(:cod_boleto)
        ";

        $dados =  DB::connection('pgsql_public')
            ->select($sql, ['cod_boleto' => $codBoleto]);

        return $dados[0]->copia_cola;
    }
}
