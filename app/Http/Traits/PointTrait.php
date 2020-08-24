<?php

namespace App\Http\Traits;

use App\Point;

trait PointTrait
{
    public function addPoint($user_id, $type_id, $total, $type)
    {
        Point::create([
            'user_id' => $user_id,
            'type_id' => $type_id,
            'total' => $total,
            'type' => $type
        ]);
    }
}
