<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\MudancaDeEnderecoCentralAssinante;
use Illuminate\Database\Eloquent\Model;
use App\Repositories\Contracts\MudancaDeEnderecoCentralAssinanteRepository;
// use App\Events\UserEvents\UserCreatedEvent;

class EloquentMudancaDeEnderecoCentralAssinanteRepository extends AbstractEloquentRepository implements MudancaDeEnderecoCentralAssinanteRepository
{
    public function newMudancaEndereco($request, $codProtocolo, $isPacote){
        $newEntry = $this->save(
            array(
                "cep" => $request->cep,
                "logradouro" => $request->logradouro,
                "numero" => (int)$request->numero,
                "complemento" => $request->complemento,
                "ponto_referencia" => $request->referencia,
                "estado" => $request->estado,
                "cidade" => $request->cidade,
                "bairro" => $request->bairro,
                "cod_chamado" => (int)$codProtocolo,
                "pacote" => $isPacote
            )
        );

        return $newEntry;
    }
}