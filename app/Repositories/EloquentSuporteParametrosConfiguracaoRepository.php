<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\SuporteParametrosConfiguracao;
use Illuminate\Database\Eloquent\Model;
use App\Repositories\Contracts\SuporteParametrosConfiguracaoRepository;
// use App\Events\UserEvents\UserCreatedEvent;

class EloquentSuporteParametrosConfiguracaoRepository extends AbstractEloquentRepository implements SuporteParametrosConfiguracaoRepository
{
}