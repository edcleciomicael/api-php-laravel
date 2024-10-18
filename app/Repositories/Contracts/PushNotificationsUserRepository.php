<?php //app/Repositories/Contracts/PushNotificationsUserRepositoryRepository.php

namespace App\Repositories\Contracts;

interface PushNotificationsUserRepository extends BaseRepository
{
    public function markAsRead($notification_id);
}