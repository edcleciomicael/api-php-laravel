<?php //app/Repositories/Contracts/ProvedorConfiguracaoClienteRepository.php

namespace App\Repositories\Contracts;

interface ProvedorConfiguracaoClienteRepository extends BaseRepository
{
    public function porServico($codServicoContrato);
}