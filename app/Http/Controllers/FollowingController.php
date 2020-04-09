<?php

namespace App\Http\Controllers;

use App\Following;
use App\User;
use Illuminate\Http\Request;

class FollowingController extends Controller
{
    public function showFollowings(Request $request, $user_id) {
        $followings = Following::with([
            'following_info'
        ])->where('user_id', $user_id)
          ->orderBy('created_at', 'desc')
          ->paginate(30);

        return response()
            ->json([
                'status' => 101,
                'message' => 'Followings Retrieved',
                'results' => $followings
            ]);
    }

    public function showFollowers(Request $request, $user_id) {
        $followers = Following::with([
            'follower_info'
        ])->where('following_id', $user_id)
          ->orderBy('created_at', 'desc')
          ->paginate(30);

        return response()
            ->json([
                'status' => 101,
                'message' => 'Followers Retrieved',
                'results' => $followers
            ]);
    }

    public function create(Request $request, $user_id) {
        $auth = User::where([
            'id' => $request['auth_id'],
            'token' => $request['auth_token']
        ])->exists();
        
        if ($auth == true) {
            $is_self = $user_id == $request['auth_id'];

            $already_followed = Following::where([
                'user_id' => $request['auth_id'],
                'following_id' => $user_id
            ])->exists();

            if ($is_self == true) {
                return response()
                    ->json([
                        'status' => 505,
                        'message' => 'Can\'t Follow Self'
                    ]);
            } else if ($already_followed == true) {
                return response()
                    ->json([
                        'status' => 505,
                        'message' => 'User Already Followed'
                    ]);
            } else {
                Following::create([
                    'user_id' => $request['auth_id'],
                    'following_id' => $user_id
                ]);
    
                return response()
                    ->json([
                        'status' => 202,
                        'message' => 'User Followed'
                    ]);
            }
        } else {
            return response()
                ->json([
                    'status' => 505,
                    'message' => 'Not Authorized to Follow User'
                ]);
        }
    }

    public function delete(Request $request, $user_id) {
        $auth = User::where([
            'id' => $request['auth_id'],
            'token' => $request['auth_token']
        ])->exists();
        
        if ($auth == true) {
            $is_following = Following::where([
                'user_id' => $request['auth_id'],
                'following_id' => $user_id
            ])->exists();

            $following_id = Following::select('id')
                ->where([
                    'user_id' => $request['auth_id'],
                    'following_id' => $user_id
                ])->firstOrFail();

            if ($is_following == true) {
                Following::findOrFail($following_id['id'])
                    ->delete();
                
                return response()
                    ->json([
                        'status' => 404,
                        'message' => 'User Unfollowed'
                    ]);
            } else {
                return response()
                    ->json([
                        'status' => 505,
                        'message' => 'User Isn\'t Followed'
                    ]);
            }
        } else {
            return response()
                ->json([
                    'status' => 505,
                    'message' => 'Not Authorized to Unfollow User'
                ]);
        }
    }
}
