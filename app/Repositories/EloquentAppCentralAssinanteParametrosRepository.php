<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\AppCentralAssinanteParametros;
use Illuminate\Database\Eloquent\Model;
use App\Repositories\Contracts\AppCentralAssinanteParametrosRepository;
// use App\Events\UserEvents\UserCreatedEvent;

class EloquentAppCentralAssinanteParametrosRepository extends AbstractEloquentRepository implements AppCentralAssinanteParametrosRepository
{
}