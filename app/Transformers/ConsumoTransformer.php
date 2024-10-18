<?php

namespace App\Transformers;

use App\Models\Radacct;
use League\Fractal\TransformerAbstract;

class ConsumoTransformer extends TransformerAbstract
{
    public function transform(Radacct $radacct)
    {
        $formatted = [
            'download' => $radacct->download,
            'upload' => $radacct->upload,
            'unix_timestamp' => $radacct->unix_timestamp,
        ];

        return $formatted;
    }
}