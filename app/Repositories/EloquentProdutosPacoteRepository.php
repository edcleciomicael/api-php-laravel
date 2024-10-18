<?php

namespace App\Repositories;

use App\Models\User;
use App\Models\ProdutosPacote;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Model;
use App\Repositories\Contracts\ProdutosPacoteRepository;
use Illuminate\Support\Facades\DB;
// use App\Events\UserEvents\UserCreatedEvent;

class EloquentProdutosPacoteRepository extends AbstractEloquentRepository implements ProdutosPacoteRepository
{
    /**
     * @inheritdoc
     */
}