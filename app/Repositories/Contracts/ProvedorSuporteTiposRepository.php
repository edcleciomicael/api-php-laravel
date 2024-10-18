<?php //app/Repositories/Contracts/ProvedorSuporteTiposRepository.php

namespace App\Repositories\Contracts;

interface ProvedorSuporteTiposRepository extends BaseRepository
{
    public function prazoMaximoEmDias($id);
    
    public function tipoPrazoDias($id);
}