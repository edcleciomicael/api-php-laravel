<?php //app/Repositories/Contracts/MudancaDeEnderecoCentralAssinanteRepository.php

namespace App\Repositories\Contracts;

interface MudancaDeEnderecoCentralAssinanteRepository extends BaseRepository
{
    public function newMudancaEndereco($request, $codProtocolo, $isPacote);
}