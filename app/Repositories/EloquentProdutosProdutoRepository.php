<?php

namespace App\Repositories;

use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Model;
use App\Repositories\Contracts\ProdutosProdutoRepository;
use Illuminate\Support\Facades\DB;

class EloquentProdutosProdutoRepository extends AbstractEloquentRepository implements ProdutosProdutoRepository
{
    public function findBy(array $searchCriteria = [])
    {
        $queryBuilder = $this->model->where(function ($query) use ($searchCriteria) {

            $this->applySearchCriteriaInQueryBuilder($query, $searchCriteria);
            
        });

        return $queryBuilder->orderBy('nome_produto')->get();
    }

    function getPlanos($codUnidade, $codServicoContrato){
        return $this->model->getPlanos($codUnidade, $codServicoContrato);
    }
}
