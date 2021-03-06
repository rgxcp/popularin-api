<?php

namespace App\Http\Controllers;

use App\Point;
use Illuminate\Support\Carbon;

class PointController extends Controller
{
    public function showsPointActivity($user_id)
    {
        Carbon::setLocale('id');

        $pointActivities = Point::where('user_id', $user_id)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'status' => isset($pointActivities[0]) ? 101 : 606,
            'message' => isset($pointActivities[0]) ? 'Request Retrieved' : 'Request Not Found',
            'result' => isset($pointActivities[0]) ? $pointActivities : null
        ]);
    }
}
