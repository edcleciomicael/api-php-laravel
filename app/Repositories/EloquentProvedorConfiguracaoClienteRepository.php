<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\ProvedorConfiguracaoCliente;
use Illuminate\Database\Eloquent\Model;
use App\Repositories\Contracts\ProvedorConfiguracaoClienteRepository;
// use App\Events\UserEvents\UserCreatedEvent;

class EloquentProvedorConfiguracaoClienteRepository extends AbstractEloquentRepository implements ProvedorConfiguracaoClienteRepository
{
    /**
     * @inheritdoc
     */
    public function porServico($codServicoContrato)
	{
        return $this->model
        ->where("cod_servico_contrato", $codServicoContrato)
        ->first();
	}

}
