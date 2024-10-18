<?php //app/Repositories/Contracts/FinanceiroCartaoCreditoTransacaoRepository.php

namespace App\Repositories\Contracts;

interface FinanceiroCartaoCreditoTransacaoRepository extends BaseRepository
{
    public function createRow($codBoleto, $valorBoleto, $codCartao);

    public function checkTransaction($codBoleto);
}