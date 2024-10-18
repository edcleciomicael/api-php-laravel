<?php

namespace App\Repositories;

use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Model;
use App\Repositories\Contracts\DevicesUsersRepository;
use Illuminate\Support\Facades\DB;

class EloquentDevicesUsersRepository extends AbstractEloquentRepository implements DevicesUsersRepository
{
    public function createRow($fcmToken, $codPessoa){

        $sql = "
            SELECT
                *
            FROM devices_users
            WHERE user_id <> {$codPessoa} AND fcm_token = '{$fcmToken}'
        ";

        //VERIFICAR PARA NÃO CRIAR LINHA3 SE FOR UMA PESSOA QUE JÁ EXISTE NA TABELA, MAS APENAS ALTERÁ-LA PARA TRUE

        $sameFcmToken = DB::connection('pgsql_api_central_cliente')->select($sql);

        // return "aaa";
        foreach ($sameFcmToken as $key => $value) {
            # code...
            $this->update($this->findOne($value->id), array("enabled" => false));
        }

        $oldEntry = $this->findOneBy(array("fcm_token" => $fcmToken, "user_id" => $codPessoa, "enabled" => false));

        if($oldEntry){
            $this->update($this->findOne($oldEntry->id), array("enabled" => true));

            return 'Usuário do Device id alterado com sucesso!';
        }

        // return $sameFcmToken;
        // if()

        $oldEntry = $this->findOneBy(array("fcm_token" => $fcmToken, "user_id" => $codPessoa, "enabled" => true));

        if(!$oldEntry){
            $newEntry = $this->save(
                array(
                    "fcm_token" => $fcmToken,
                    "user_id" => $codPessoa,
                    "enabled" => true,
                )
            );
            return 'Usuário para o Device id cadastrado com sucesso!';
        }

        return 'Nenhuma alteração foi feita!';
    }

    public function disableRow($fcmToken, $codPessoa){
        $entry = $this->findOneBy(array("fcm_token" => $fcmToken, "user_id" => $codPessoa, "enabled" => true));

        if($entry){
            $this->update($entry, array("enabled" => false));

            return 'Usuário do Device id desativado com sucesso!';
        }

        return 'Usuário do Device id não foi desativado!';
    }
}