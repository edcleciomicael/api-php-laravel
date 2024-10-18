<?php //app/Repositories/Contracts/ProvedorFibraONUWifiRepository.php

namespace App\Repositories\Contracts;

interface ProvedorFibraONUWifiRepository extends BaseRepository
{
    public function redeWifi($codConfiguracaoCliente);
}