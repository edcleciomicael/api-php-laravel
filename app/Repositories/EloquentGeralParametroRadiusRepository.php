<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\GeralParametroRadius;
use Illuminate\Database\Eloquent\Model;
use App\Repositories\Contracts\GeralParametroRadiusRepository;
// use App\Events\UserEvents\UserCreatedEvent;

class EloquentGeralParametroRadiusRepository extends AbstractEloquentRepository implements GeralParametroRadiusRepository
{
}