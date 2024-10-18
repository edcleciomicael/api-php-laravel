<?php //app/Repositories/Contracts/DevicesUsersRepository.php

namespace App\Repositories\Contracts;

interface DevicesUsersRepository extends BaseRepository
{
    public function createRow($fcmToken, $codPessoa);
    public function disableRow($fcmToken, $codPessoa);
}