<?php

namespace App\Repositories;

use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Model;
use App\Repositories\Contracts\FinanceiroCartaoCreditoRepository;

class EloquentFinanceiroCartaoCreditoRepository extends AbstractEloquentRepository implements FinanceiroCartaoCreditoRepository
{
    public function createRow($codPessoaContrato, $bandeira, $prioritario, $isCredito, $encryptedData){
        $newEntry = $this->save(
            array(
                "cod_pessoa_contrato" => $codPessoaContrato,
                "origem" => 3,
                "bandeira" => $bandeira,
                "prioritario" => $prioritario,
                "create_at" => date("Y-m-d H:i:s"),
                "dados_encriptados" => $encryptedData,
                "credito" => $isCredito,
            )
        );

        return $newEntry;
    }

    public function findBy(array $searchCriteria = [])
    {
        $queryBuilder = $this->model->where(function ($query) use ($searchCriteria) {

            $this->applySearchCriteriaInQueryBuilder($query, $searchCriteria);
            
        });

        return $queryBuilder->get();
    }
}