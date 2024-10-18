<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\Radacct;
use Illuminate\Database\Eloquent\Model;
use App\Repositories\Contracts\RadacctRepository;
// use App\Events\UserEvents\UserCreatedEvent;

class EloquentRadacctRepository extends AbstractEloquentRepository implements RadacctRepository
{
    /**
     * @inheritdoc
     */
    public function consumo($codConfiguracaoCliente)
	{
		return $this->model->
			// whereHas('contratosServicosContrato', function ($query) {
			// 	$query->whereHas('produto', function ($query) {
			// 		$query
			// 			->where('acesso', true)
			// 			->where('tv', false);
			// 	});
			// })
			// ->
			select(DB::raw("
				sum(radacct.acctoutputoctets) AS download, 
				sum(radacct.acctinputoctets) AS upload, 
				radacct.cod_configuracao_cliente, 
				to_char(acctstarttime::date, 'MM/YYYY'), 
				extract(epoch from to_char(acctstarttime::date, 'YYYY-MM-01')::date)*1000 as unix_timestamp
			"))
			->where("cod_configuracao_cliente", $codConfiguracaoCliente)
			// ->where("acctstarttime", ">", "NOW()")
			// ->whereRaw("acctstarttime > NOW() - '12 months'::interval")
			->groupBy(DB::raw("
				radacct.cod_configuracao_cliente,
				extract(month from acctstarttime::date),
				to_char(acctstarttime::date, 'MM/YYYY'),
				to_char(acctstarttime::date, 'YYYY-MM-01')
			"))
			->orderBy(DB::raw("5"))
			->get();
	}

}
