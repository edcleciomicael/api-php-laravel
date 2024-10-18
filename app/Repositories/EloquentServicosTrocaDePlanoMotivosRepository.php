<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\ServicosTrocaDePlanoMotivos;
use Illuminate\Database\Eloquent\Model;
use App\Repositories\Contracts\ServicosTrocaDePlanoMotivosRepository;
// use App\Events\UserEvents\UserCreatedEvent;

class EloquentServicosTrocaDePlanoMotivosRepository extends AbstractEloquentRepository implements ServicosTrocaDePlanoMotivosRepository
{
    public function findOne($id)
    {
        return $this->findOneBy(['cod_troca_de_plano_motivo_central_assinante' => $id]);
    }
}