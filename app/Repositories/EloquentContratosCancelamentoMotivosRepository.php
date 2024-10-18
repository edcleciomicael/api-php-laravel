<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\ContratosCancelamentoMotivos;
use Illuminate\Database\Eloquent\Model;
use App\Repositories\Contracts\ContratosCancelamentoMotivosRepository;
// use App\Events\UserEvents\UserCreatedEvent;

class EloquentContratosCancelamentoMotivosRepository extends AbstractEloquentRepository implements ContratosCancelamentoMotivosRepository
{
    public function findOne($id)
    {
        return $this->findOneBy(['cod_cancelamento_motivo_central_assinante' => $id]);
    }
}