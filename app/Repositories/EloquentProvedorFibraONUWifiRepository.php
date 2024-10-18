<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\ProvedorFibraONUWifi;
use Illuminate\Database\Eloquent\Model;
use App\Repositories\Contracts\ProvedorFibraONUWifiRepository;
// use App\Events\UserEvents\UserCreatedEvent;

class EloquentProvedorFibraONUWifiRepository extends AbstractEloquentRepository implements ProvedorFibraONUWifiRepository
{
    /**
     * @inheritdoc
     */
    public function redeWifi($codConfiguracaoCliente)
	{
		return $this->model->where("cod_configuracao_cliente", $codConfiguracaoCliente)->first();
	}

}
