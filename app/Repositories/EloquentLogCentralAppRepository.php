<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\LogCentralApp;
use Illuminate\Database\Eloquent\Model;
use App\Repositories\Contracts\LogCentralAppRepository;
// use App\Events\UserEvents\UserCreatedEvent;

class EloquentLogCentralAppRepository extends AbstractEloquentRepository implements LogCentralAppRepository
{
    public function addLog($acao, $registro = null){
        DB::beginTransaction();
        try {
            
            $log = $this->save(
                array(
                    "cod_pessoa" => $this->loggedInUser->cod_pessoa,
                    "acao" => $acao,
                    "registro" => $registro,
                    "created_at" => 'now()',
                )
            );

            DB::commit();
        } catch (Exception $e) {
            DB::rollback();

            throw new Exception($e->getMessage(), $e->getCode());
        }
        

        return $log;
    }
}