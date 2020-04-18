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

        return response()->json([
            'status' => isset($followings[0]) ? 701 : 979,
            'message' => isset($followings[0]) ? 'Followings Retrieved' : 'Empty Followings',
            'result' => isset($followings[0]) ? $followings : null
        ]);
    }

    public function showFollowers($user_id) {
        $followers = Following::with([
            'follower'
        ])->where('following_id', $user_id)
          ->orderBy('created_at', 'desc')
          ->paginate(30);

        return response()->json([
            'status' => isset($followers[0]) ? 711 : 989,
            'message' => isset($followers[0]) ? 'Followers Retrieved' : 'Empty Followers',
            'result' => isset($followers[0]) ? $followers : null
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
            User::findOrFail($user_id);

            $already_followed = Following::where([
                'user_id' => $auth_uid,
                'following_id' => $user_id
            ])->exists();

            if ($auth_uid == $user_id) {
                return response()->json([
                    'status' => 907,
                    'message' => 'Can\'t Follow Self'
                ]);
            } else if ($already_followed == true) {
                return response()->json([
                    'status' => 927,
                    'message' => 'User Already Followed'
                ]);
            } else {
                Following::create([
                    'user_id' => $auth_uid,
                    'following_id' => $user_id
                ]);
    
                return response()->json([
                    'status' => 702,
                    'message' => 'User Followed'
                ]);
            }
        } else {
            return response()->json([
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
            ])->firstOrFail()
              ->delete();

            return response()->json([
                'status' => 704,
                'message' => 'User Unfollowed'
            ]);
        } else {
            return response()->json([
                'status' => 808,
                'message' => 'Invalid Credentials'
            ]);
        }
    }
}
