<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\ContratosPessoaContrato;
use Illuminate\Database\Eloquent\Model;
use App\Repositories\Contracts\ContratosPessoaContratoRepository;
// use App\Events\UserEvents\UserCreatedEvent;

class EloquentContratosPessoaContratoRepository extends AbstractEloquentRepository implements ContratosPessoaContratoRepository
{
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

    public function getContratos($cancelados){
        // Only admin user can see all users
        if ($this->loggedInUser->role !== User::ADMIN_ROLE) {
            
            if($cancelados){
                return $this->model
                    ->join('pessoa', 'pessoa.cod_pessoa', '=', 'contratos_pessoa_contrato.cod_pessoa')
                    ->join('contratos_servicos_contrato', 'contratos_pessoa_contrato.cod_pessoa_contrato', '=', 'contratos_servicos_contrato.cod_pessoa_contrato')
                    ->leftJoin('provedor_configuracao_cliente', 'contratos_servicos_contrato.cod_servico_contrato', '=', 'provedor_configuracao_cliente.cod_servico_contrato')
                    ->join('bairro_cidade', 'bairro_cidade.cod_bairro_cidade', '=', 'contratos_pessoa_contrato.cod_bairro_cidade')
                    ->join('cidade', 'bairro_cidade.cod_cidade', '=', 'cidade.cod_cidade')
                    ->join('contratos_status_contrato', 'contratos_pessoa_contrato.cod_status_contrato', '=', 'contratos_status_contrato.cod_status_contrato')
                    ->where("pessoa.cpf_cnpj", $this->loggedInUser->cpf_cnpj)
                    ->whereRaw("
                        (
                            ((select exists(SELECT ativo FROM app_central_assinante_parametros WHERE cod_parametro = 26)) AND (SELECT ativo FROM app_central_assinante_parametros WHERE cod_parametro = 26) AND  (SELECT pcc.cod_servico_contrato FROM provedor_configuracao_cliente pcc WHERE pcc.cod_servico_contrato = contratos_servicos_contrato.cod_servico_contrato) is not null)
                            OR
                            (CASE WHEN (select exists(SELECT ativo FROM app_central_assinante_parametros WHERE cod_parametro = 26)) THEN not (SELECT ativo FROM app_central_assinante_parametros WHERE cod_parametro = 26) ELSE true END)
                        )
                    ")
                    // ->where("cod_pessoa", $this->loggedInUser->cod_pessoa)->get();
                    ->groupBy('contratos_pessoa_contrato.cod_pessoa_contrato')->groupBy('pessoa.cod_pessoa')
                    ->groupBy('bairro_cidade.bairro')->groupBy('cidade.nome_cidade')->groupBy('cidade.uf')
                    ->groupBy('contratos_status_contrato.status_contrato')
                    ->havingRaw('
                        (
                            SELECT
                                count(csc1.cod_servico_contrato)
                            FROM contratos_servicos_contrato csc1
                            WHERE
                                csc1.cod_pessoa_contrato = contratos_pessoa_contrato.cod_pessoa_contrato
                                AND csc1.acesso_central_assinante is true
                                AND csc1.parcelas = 0
                        ) > 0
                    ')->select(
                        'contratos_pessoa_contrato.cod_pessoa_contrato',
                        'pessoa.cpf_cnpj',
                        'contratos_pessoa_contrato.endereco',
                        'contratos_pessoa_contrato.numero',
                        'contratos_pessoa_contrato.complemento',
                        'contratos_pessoa_contrato.cep',
                        'bairro_cidade.bairro',
                        'cidade.nome_cidade',
                        'cidade.uf',
                        'contratos_pessoa_contrato.email',
                        'contratos_pessoa_contrato.telefone',
                        'contratos_pessoa_contrato.cod_unidade',
                        'contratos_pessoa_contrato.cod_status_contrato',
                        'contratos_status_contrato.status_contrato',
                        'apelido'
                    )->get();
            }

            // return $this->model
                    // ->join('pessoa', 'pessoa.cod_pessoa', '=', 'contratos_pessoa_contrato.cod_pessoa')
                    // ->where("pessoa.cpf_cnpj", $this->loggedInUser->cpf_cnpj)
                    // // ->where("contratos_pessoa_contrato.cod_pessoa", $this->loggedInUser->cod_pessoa)
                    // ->where("cod_status_contrato", "<>", 2 )
                    // ->groupBy('contratos_pessoa_contrato.cod_pessoa_contrato')->groupBy('pessoa.cod_pessoa')
                    // ->havingRaw('
                    //     (
                    //         SELECT
                    //             count(csc1.cod_servico_contrato)
                    //         FROM contratos_servicos_contrato csc1
                    //         WHERE
                    //             csc1.cod_pessoa_contrato = contratos_pessoa_contrato.cod_pessoa_contrato
                    //             AND csc1.acesso_central_assinante is true
                    //             AND csc1.parcelas = 0
                    //     ) > 0
                    // ')->get();
                return $this->model
                    ->join('pessoa', 'pessoa.cod_pessoa', '=', 'contratos_pessoa_contrato.cod_pessoa')
                    ->join('contratos_servicos_contrato', 'contratos_pessoa_contrato.cod_pessoa_contrato', '=', 'contratos_servicos_contrato.cod_pessoa_contrato')
                    ->leftJoin('provedor_configuracao_cliente', 'contratos_servicos_contrato.cod_servico_contrato', '=', 'provedor_configuracao_cliente.cod_servico_contrato')
                    ->join('bairro_cidade', 'bairro_cidade.cod_bairro_cidade', '=', 'contratos_pessoa_contrato.cod_bairro_cidade')
                    ->join('cidade', 'bairro_cidade.cod_cidade', '=', 'cidade.cod_cidade')
                    ->join('contratos_status_contrato', 'contratos_pessoa_contrato.cod_status_contrato', '=', 'contratos_status_contrato.cod_status_contrato')
                    ->where("pessoa.cpf_cnpj", $this->loggedInUser->cpf_cnpj)
                    ->where("contratos_pessoa_contrato.cod_status_contrato", "<>", 2 )
                    ->whereRaw("
                        (
                            ((select exists(SELECT ativo FROM app_central_assinante_parametros WHERE cod_parametro = 26)) AND (SELECT ativo FROM app_central_assinante_parametros WHERE cod_parametro = 26) AND  (SELECT pcc.cod_servico_contrato FROM provedor_configuracao_cliente pcc WHERE pcc.cod_servico_contrato = contratos_servicos_contrato.cod_servico_contrato) is not null)
                            OR
                            (CASE WHEN (select exists(SELECT ativo FROM app_central_assinante_parametros WHERE cod_parametro = 26)) THEN not (SELECT ativo FROM app_central_assinante_parametros WHERE cod_parametro = 26) ELSE true END)
                        )
                    ")
                    // ->where("cod_pessoa", $this->loggedInUser->cod_pessoa)->get();
                    ->groupBy('contratos_pessoa_contrato.cod_pessoa_contrato')->groupBy('pessoa.cod_pessoa')
                    ->groupBy('bairro_cidade.bairro')->groupBy('cidade.nome_cidade')->groupBy('cidade.uf')
                    ->groupBy('contratos_status_contrato.status_contrato')
                    ->havingRaw('
                        (
                            SELECT
                                count(csc1.cod_servico_contrato)
                            FROM contratos_servicos_contrato csc1
                            WHERE
                                csc1.cod_pessoa_contrato = contratos_pessoa_contrato.cod_pessoa_contrato
                                AND csc1.acesso_central_assinante is true
                                AND csc1.parcelas = 0
                        ) > 0
                    ')->select(
                        'contratos_pessoa_contrato.cod_pessoa_contrato',
                        'pessoa.cpf_cnpj',
                        'contratos_pessoa_contrato.endereco',
                        'contratos_pessoa_contrato.numero',
                        'contratos_pessoa_contrato.complemento',
                        'contratos_pessoa_contrato.cep',
                        'bairro_cidade.bairro',
                        'cidade.nome_cidade',
                        'cidade.uf',
                        'contratos_pessoa_contrato.email',
                        'contratos_pessoa_contrato.telefone',
                        'contratos_pessoa_contrato.cod_unidade',
                        'contratos_pessoa_contrato.cod_status_contrato',
                        'contratos_status_contrato.status_contrato',
                        'apelido'
                    )->get();
        }

        return $this->model->get();
    }

    public function getDetalhesContrato($codPessoaContrato){
        return $this->model->detalhesContrato($codPessoaContrato);
    }

    public function updateEmails($request, $codPessoaContrato){
        $emails = $request->email;

        // return $emails;

        $newEmails = array();

        foreach ($emails as $key => $value) {
            array_push($newEmails, $value['email']);
        }

        $newEmails = implode('|', $newEmails);

        // return $newEmails;

        DB::beginTransaction();
        try {
            //code...
            $contrato = $this->findOne($codPessoaContrato);

            $contrato = $this->update($contrato, array("email" => $newEmails));

            DB::commit();
    
        } catch (Exception $e) {
            DB::rollback();

            throw new Exception($e->getMessage(), $e->getCode());
        }

        return $contrato;
        
    }

    public function updateApelido($apelido, $codPessoaContrato){
        DB::beginTransaction();
        try {
            
            $contrato = $this->findOne($codPessoaContrato);
            $contrato = $this->update($contrato, array("apelido" => $apelido));

            DB::commit();
        } catch (Exception $e) {
            DB::rollback();

            throw new Exception($e->getMessage(), $e->getCode());
        }

        return $contrato;
    }

}