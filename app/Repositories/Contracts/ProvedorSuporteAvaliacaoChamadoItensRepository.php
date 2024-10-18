<?php //app/Repositories/Contracts/ProvedorSuporteAvaliacaoChamadoItensRepository.php

namespace App\Repositories\Contracts;

interface ProvedorSuporteAvaliacaoChamadoItensRepository extends BaseRepository
{
    public function createRow($resposta, $codAvaliacaoChamado);
}