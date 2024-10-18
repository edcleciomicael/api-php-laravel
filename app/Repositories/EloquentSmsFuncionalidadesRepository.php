<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\SmsFuncionalidades;
use Illuminate\Database\Eloquent\Model;
use App\Repositories\Contracts\SmsFuncionalidadesRepository;
// use App\Events\UserEvents\UserCreatedEvent;

class EloquentSmsFuncionalidadesRepository extends AbstractEloquentRepository implements SmsFuncionalidadesRepository
{

}