<?php

namespace App\Repositories;

use App\Models\User;
use App\Models\FinanceiroLancamentos;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Model;
use App\Repositories\Contracts\FinanceiroLancamentosRepository;
use Illuminate\Support\Facades\DB;
// use App\Events\UserEvents\UserCreatedEvent;

class EloquentFinanceiroLancamentosRepository extends AbstractEloquentRepository implements FinanceiroLancamentosRepository
{

    /*
     * @inheritdoc
     */
    public function save(array $data)
    {
        // update password
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $user = parent::save($data);

        // fire user created event
        // \Event::fire(new UserCreatedEvent($user));

        return $user;
    }

    /**
     * @inheritdoc
     */
    public function update(Model $model, array $data)
    {
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $updatedUser = parent::update($model, $data);

        return $updatedUser;
    }

    /**
     * @inheritdoc
     */
    public function findBy(array $searchCriteria = [])
    {
        // Only admin user can see all users
        if ($this->loggedInUser->role !== User::ADMIN_ROLE) {
            
            $searchCriteria['cod_pessoa'] = $this->loggedInUser->cod_pessoa;
        }

        return parent::findBy($searchCriteria);
    }

    /**
     * @inheritdoc
     */
    public function findOne($id)
    {
        if ($id === 'me') {
            return $this->getLoggedInUser();
        }

        return parent::findOne($id);
    }

    public function lancamentosPorBoleto($codBoleto) {
        $sql = "
            SELECT fl.cod_lancamento, 
                case when length(pp.nome_produto) > 0 
                    then 
                        pp.nome_produto 
                    else 
                        fl.descricao_lancamento 
                end as descricao, 
                fl.valor_unitario, 
                cp.numero_parcela, 
                cp.periodo_apuracao,

                -- RELAÇÃO GNET
                ib.carne_parcela as integrado_carne_parcela,
                fbancos.cod_febraban,
                -- FIM: RELAÇÃO GNET

                fl.data_vencimento_valido
                
            from financeiro_cobranca fc
            left join contratos_parcelas_lancamento cpl on (cpl.cod_lancamento = fc.cod_lancamento)
            left join contratos_parcelas cp on (cp.cod_parcela = cpl.cod_parcela)
            left join contratos_servicos_contrato csc on (csc.cod_servico_contrato = cp.cod_servico_contrato)
            left join produtos_produto pp on (pp.cod_produto = csc.cod_produto)

            -- RELAÇÃO GNET
            left join financeiro_boletos fb on fb.cod_boleto = fc.cod_boleto
            left join financeiro_convenio_bancario fcb on fcb.cod_convenio_bancario = fb.cod_convenio_bancario
            left join financeiro_banco as fbancos on fbancos.cod_banco = fcb.cod_banco
            LEFT JOIN integrados_boleto ib ON ib.boleto_logica = fb.cod_boleto AND
            (CASE WHEN exists(
                SELECT * FROM public.integrados_gnet_acao iga
                WHERE
                fb.cod_boleto = any (iga.boletos)
                AND iga.deleted_at is null
                AND iga.processando = 'f'
            ) THEN TRUE ELSE FALSE END) = FALSE
            -- FIM: RELAÇÃO GNET

            inner join financeiro_lancamentos fl on (fl.cod_lancamento = fc.cod_lancamento)
            where fc.cod_boleto = :cod_boleto;
        ";
        return DB::connection('pgsql_public')->select($sql, ['cod_boleto' => $codBoleto]);
    }

}