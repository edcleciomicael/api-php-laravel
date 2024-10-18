<?php //app/Repositories/Contracts/ContratosServicosContratoRepository.php

namespace App\Repositories\Contracts;

interface ContratosServicosContratoRepository extends BaseRepository
{
    public function porContrato($codPessoaContrato);

    public function porContratoComCancelamento($codPessoaContrato);

    public function porContratoComTrocaDePlano($codPessoaContrato);

    public function porContratoComConfiguracao($codPessoaContrato);

    public function porContratoAberturaChamado($codPessoaContrato);

    public function porContratoComTrocaDeEndereco($codPessoaContrato);

}