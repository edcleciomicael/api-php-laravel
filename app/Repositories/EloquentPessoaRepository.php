<?php

namespace App\Repositories;

use App\Models\User;
use App\Models\Pessoa;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Model;
use App\Repositories\Contracts\PessoaRepository;
use Illuminate\Support\Facades\DB;
// use App\Events\UserEvents\UserCreatedEvent;

class EloquentPessoaRepository extends AbstractEloquentRepository implements PessoaRepository
{
    /**
     * @inheritdoc
     */
    public function pessoaLogada()
    {
        $searchCriteria['cod_pessoa'] = $this->loggedInUser->cod_pessoa;

        return parent::findOneBy($searchCriteria);
    }
}