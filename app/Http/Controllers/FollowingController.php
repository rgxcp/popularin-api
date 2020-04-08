<?php

namespace App\Http\Controllers;

use App\Following;
use Illuminate\Http\Request;

class FollowingController extends Controller
{
    public function showFollowings(Request $request, $user_id) {}

    public function showFollowers(Request $request, $user_id) {}

    public function create(Request $request, $user_id) {}

    public function delete(Request $request, $user_id) {}
}
