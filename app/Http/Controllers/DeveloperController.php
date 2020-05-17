<?php

namespace App\Http\Controllers;

class DeveloperController extends Controller
{
    public function showStatus() {
        return response()->json([
            101 => 'Request Retrieved',
            202 => 'Request Created',
            303 => 'Request Updated',
            404 => 'Request Deleted',
            505 => 'User Signed Up',
            515 => 'User Signed In',
            525 => 'User Signed Out',
            606 => 'Request Not Found',
            616 => 'Invalid Credentials',
            626 => 'Validator Fails',
            636 => 'Can\'t Follow Self',
            646 => 'Already Favorited',
            656 => 'Already Followed',
            666 => 'Already Liked',
            676 => 'Already Watchlisted'
        ]);
    }
}
