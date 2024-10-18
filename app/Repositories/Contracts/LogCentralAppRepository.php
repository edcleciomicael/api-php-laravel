<?php //app/Repositories/Contracts/LogCentralAppRepository.php

namespace App\Repositories\Contracts;

interface LogCentralAppRepository extends BaseRepository
{
    public function addLog($acao, $registro = null);
}