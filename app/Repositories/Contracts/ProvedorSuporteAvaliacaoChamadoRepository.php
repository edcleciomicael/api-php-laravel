<?php //app/Repositories/Contracts/ProvedorSuporteAvaliacaoChamadoRepository.php

namespace App\Repositories\Contracts;

interface ProvedorSuporteAvaliacaoChamadoRepository extends BaseRepository
{
    public function createRow($row);
}