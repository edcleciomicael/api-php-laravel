<?php //app/Repositories/Contracts/ProvedorSuporteChamadoRepository.php

namespace App\Repositories\Contracts;

interface ProvedorSuporteChamadoRepository extends BaseRepository
{
    public function suportesPorContrato($codPessoaContrato);
    public function suporteTrocaDeEndereco($codServicoContrato);
}