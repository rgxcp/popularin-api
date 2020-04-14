<?php

namespace App\Http\Controllers;

use App\Following;
use App\User;
use Illuminate\Http\Request;

class FollowingController extends Controller
{
    public function showFollowings($user_id) {
        $followings = Following::with([
            'following'
        ])->where('user_id', $user_id)
          ->orderBy('created_at', 'desc')
          ->paginate(30);

        return response()
            ->json([
                'status' => 701,
                'message' => 'Followings Retrieved',
                'results' => $followings
            ]);
    }

    public function showFollowers($user_id) {
        $followers = Following::with([
            'follower'
        ])->where('following_id', $user_id)
          ->orderBy('created_at', 'desc')
          ->paginate(30);

        return response()
            ->json([
                'status' => 711,
                'message' => 'Followers Retrieved',
                'results' => $followers
            ]);
    }

    public function create(Request $request, $user_id) {
        $auth_uid = $request->header('auth_uid');
        $auth_token = $request->header('auth_token');
        
        $auth = User::where([
            'id' => $auth_uid,
            'token' => $auth_token
        ])->exists();
        
        if ($auth == true) {
            $already_followed = Following::where([
                'user_id' => $auth_uid,
                'following_id' => $user_id
            ])->exists();

            $user_exist = User::where([
                'id' => $user_id
            ])->exists();

            if ($user_id == $auth_uid) {
                return response()
                    ->json([
                        'status' => 907,
                        'message' => 'Can\'t Follow Self'
                    ]);
            } else if ($already_followed == true) {
                return response()
                    ->json([
                        'status' => 927,
                        'message' => 'User Already Followed'
                    ]);
            } else if ($user_exist == false) {
                return response()
                    ->json([
                        'status' => 929,
                        'message' => 'User Not Found'
                    ]);
            } else {
                Following::create([
                    'user_id' => $auth_uid,
                    'following_id' => $user_id
                ]);
    
                return response()
                    ->json([
                        'status' => 702,
                        'message' => 'User Followed'
                    ]);
            }
        } else {
            return response()
                ->json([
                    'status' => 808,
                    'message' => 'Invalid Credentials'
                ]);
        }
    }

    public function delete(Request $request, $user_id) {
        $auth_uid = $request->header('auth_uid');
        $auth_token = $request->header('auth_token');
        
        $auth = User::where([
            'id' => $auth_uid,
            'token' => $auth_token
        ])->exists();
        
        if ($auth == true) {
            Following::where([
                'user_id' => $auth_uid,
                'following_id' => $user_id
            ])->delete();

            return response()
                ->json([
                    'status' => 704,
                    'message' => 'User Unfollowed'
                ]);
        } else {
            return response()
                ->json([
                    'status' => 808,
                    'message' => 'Invalid Credentials'
                ]);
        }
    }
}
