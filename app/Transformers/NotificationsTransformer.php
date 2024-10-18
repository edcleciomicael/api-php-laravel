<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;

class NotificationsTransformer extends TransformerAbstract
{
    public function transform($pushNotificationsUser)
    {
        $formattedUser = [
            "id" => $pushNotificationsUser->id,
            "push_notification_id" => $pushNotificationsUser->push_notification_id,
            "read" => $pushNotificationsUser->read,
            "header" => $pushNotificationsUser->header,
            "subheader" => $pushNotificationsUser->subheader,
            "body" => $pushNotificationsUser->body,
            "date" => date("d/m/Y", strtotime($pushNotificationsUser->created_at)),
            "codPessoaContrato" => $pushNotificationsUser->cod_pessoa_contrato,
            "codChamado" => $pushNotificationsUser->cod_chamado,
        ];

        return $formattedUser;
    }
}