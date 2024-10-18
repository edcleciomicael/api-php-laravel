<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\ContratosServicoContrato;
use App\Models\ProdutosProduto;
use App\Models\SuporteParametrosConfiguracao;
use App\Repositories\Contracts\ContratosServicosContratoRepository;
// use App\Events\UserEvents\UserCreatedEvent;

class EloquentContratosServicosContratoRepository extends AbstractEloquentRepository implements ContratosServicosContratoRepository
{
    public function findOne($id)
    {
        return $this->findOneBy(['cod_servico_contrato' => $id]);
    }
    /**
     * @inheritdoc
     */
    public function porContrato($codPessoaContrato)
    {
        return $this->model
        ->whereHas('produto', function ($query) {
            $query
                ->where('acesso', true)
                ->where('tv', false)
                ->where('acesso_central_assinante', true);
        })
        ->where("cod_pessoa_contrato", $codPessoaContrato)->get();
    }

    public function porContratoComConfiguracao($codPessoaContrato)
    {
        return $this->model
        ->whereHas('provedorConfiguracaoCliente', function ($query) {
            $query
                ->where('ativo', true)
                ->whereNotNull('username')
                ->whereNotNull('senha');
        })
        ->whereHas('produto', function ($query) {
            $query
                ->where('acesso', true)
                ->where('tv', false)
                ->where('acesso_central_assinante', true);
        })
        ->where("cod_pessoa_contrato", $codPessoaContrato)->get();
    }

    public function porContratoComCancelamento($codPessoaContrato){
        return $this->model->selectServicosCancelamento($codPessoaContrato);
    }

    public function porContratoComTrocaDePlano($codPessoaContrato){
        return $this->model->selectServicosTrocaDePlano($codPessoaContrato);
    }

    public function porContratoAberturaChamado($codPessoaContrato){
        return $this->model->selectServicosAbertura($codPessoaContrato);
    }

    public function porContratoComTrocaDeEndereco($codPessoaContrato){
        return $this->model->selectServicosTrocaDeEndereco($codPessoaContrato);
    }

}