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
            'status' => isset($followings[0]) ? 101 : 606,
            'message' => isset($followings[0]) ? 'Request Retrieved' : 'Request Not Found',
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
            'status' => isset($followers[0]) ? 101 : 606,
            'message' => isset($followers[0]) ? 'Request Retrieved' : 'Request Not Found',
            'result' => isset($followers[0]) ? $followers : null
        ]);
    }

    public function showMutuals(Request $request, $user_id) {
        $auth_uid = $request->header('auth_uid');

        $auth_followings = Following::select('following_id')->where('user_id', $auth_uid)->pluck('following_id')->toArray();
        $user_followings = Following::select('following_id')->where('user_id', $user_id)->pluck('following_id')->toArray();
        $intersect_followings = array_values(array_intersect($auth_followings, $user_followings));

        $mutuals = User::withTrashed()
            ->select('id', 'full_name', 'username', 'profile_picture')
            ->whereIn('id', $intersect_followings)
            ->orderBy('created_at', 'desc')
            ->paginate(30);

        return response()->json([
            'status' => isset($mutuals[0]) ? 101 : 606,
            'message' => isset($mutuals[0]) ? 'Request Retrieved' : 'Request Not Found',
            'result' => isset($mutuals[0]) ? $mutuals : null
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
                    'status' => 636,
                    'message' => 'Can\'t Follow Self'
                ]);
            } else if ($already_followed == true) {
                return response()->json([
                    'status' => 656,
                    'message' => 'Already Followed'
                ]);
            } else {
                Following::create([
                    'user_id' => $auth_uid,
                    'following_id' => $user_id
                ]);
    
                return response()->json([
                    'status' => 202,
                    'message' => 'Request Created'
                ]);
            }
        } else {
            return response()->json([
                'status' => 616,
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
                'status' => 404,
                'message' => 'Request Deleted'
            ]);
        } else {
            return response()->json([
                'status' => 616,
                'message' => 'Invalid Credentials'
            ]);
        }
    }
}
