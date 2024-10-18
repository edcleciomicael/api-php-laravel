<?php //app/Repositories/Contracts/FinanceiroFeriadosRepository.php

namespace App\Repositories\Contracts;

interface FinanceiroFeriadosRepository extends BaseRepository
{
    public function validadeDiasUteis($dataInicial, $prazo);
}