<?php //app/Repositories/Contracts/FinanceiroCartaoCreditoRepository.php

namespace App\Repositories\Contracts;

interface FinanceiroCartaoCreditoRepository extends BaseRepository
{
    public function createRow($codPessoaContrato, $bandeira, $prioritario, $isCredito, $encryptedData);
}