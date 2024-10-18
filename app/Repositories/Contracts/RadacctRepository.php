<?php //app/Repositories/Contracts/RadacctRepository.php

namespace App\Repositories\Contracts;

interface RadacctRepository extends BaseRepository
{
    public function consumo($codConfiguracaoCliente);
}