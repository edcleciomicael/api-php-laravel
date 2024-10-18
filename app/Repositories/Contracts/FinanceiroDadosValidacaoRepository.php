<?php //app/Repositories/Contracts/FinanceiroDadosValidacaoRepository.php

namespace App\Repositories\Contracts;

interface FinanceiroDadosValidacaoRepository extends BaseRepository
{
    public function createRow($cpfCnpj, $tipo, $contatoValidar, $origem, $codItem);
    public function verifyCode($id, $code);
}