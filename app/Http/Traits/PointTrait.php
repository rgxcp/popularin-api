<?php

namespace App\Http\Traits;

use App\Point;

trait PointTrait
{
    public function addPoint($userID, $typeID, $total, $type)
    {
        Point::create([
            'user_id' => $userID,
            'type_id' => $typeID,
            'total' => $total,
            'type' => $type
        ]);
    }
}
