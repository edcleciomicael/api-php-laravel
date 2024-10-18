<?php //app/Repositories/Contracts/ProdutosProdutoRepositoryRepository.php

namespace App\Repositories\Contracts;

interface ProdutosProdutoRepository extends BaseRepository
{
    function getPlanos($codUnidade, $codServicoContrato);
}
