<?php //app/Repositories/Contracts/ContratosPessoaContratoRepository.php

namespace App\Repositories\Contracts;

interface ContratosPessoaContratoRepository extends BaseRepository
{
    public function getContratos($cancelados);
    public function updateEmails($request, $codPessoaContrato);
    public function getDetalhesContrato($codPessoaContrato);
    public function updateApelido($apelido, $codPessoaContrato);
}