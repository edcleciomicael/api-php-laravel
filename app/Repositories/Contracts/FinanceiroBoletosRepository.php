<?php //app/Repositories/Contracts/FinanceiroBoletosRepository.php

namespace App\Repositories\Contracts;

interface FinanceiroBoletosRepository extends BaseRepository
{
    public function chamaBoletosPorContrato($codPessoaContrato, $codPessoa);
    public function chamaBoletosComNotaPorContrato($codPessoaContrato, $codPessoa);
    // public function boletoLancamentos(int $codBoleto);
    public function verificaAtraso($codPessoaContrato, $codPessoa);
    public function getCopiaCola($codBoleto);
}