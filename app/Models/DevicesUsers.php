<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DevicesUsers extends Model
{
    use HasFactory;

    protected $table = 'api_central_assinante.devices_users';
    protected $connection = 'pgsql_api_central_cliente';
    public $timestamps = false;

    protected $fillable = [
        "fcm_token",
        "user_id",
        "enabled",
    ];
}
