<?php

namespace App\Repositories;

use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Model;
use App\Repositories\Contracts\PushNotificationsUserRepository;
use Illuminate\Support\Facades\DB;

class EloquentPushNotificationsUserRepository extends AbstractEloquentRepository implements PushNotificationsUserRepository
{
    public function markAsRead($notification_id){
        $notification = $this->findOne($notification_id);

        return $this->update($notification, array("read" => true));
    }

    public function findBy(array $searchCriteria = [])
    {
        $queryBuilder = $this->model->where(function ($query) use ($searchCriteria) {

            $this->applySearchCriteriaInQueryBuilder($query, $searchCriteria);
            
        });

        return $queryBuilder->orderBy('read')->orderBy('id', 'DESC')->get();
    }
}