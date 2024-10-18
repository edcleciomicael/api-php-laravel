<?php //app/Repositories/Contracts/FinanceiroLancamentosRepository.php

namespace App\Repositories\Contracts;

interface FinanceiroLancamentosRepository extends BaseRepository
{
    public function lancamentosPorBoleto($codBoleto);
}