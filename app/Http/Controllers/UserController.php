<?php

namespace App\Http\Controllers;

use App\Favorite;
use App\Following;
use App\Review;
use App\User;
use App\Watchlist;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function show(Request $request, $id) {
        $user = User::select('id', 'first_name', 'last_name', 'profile_picture')
            ->findOrFail($id);

        $auth_id = $request->header('auth_id');

        $follows_me = Following::where([
            'user_id' => $id,
            'following_id' => $auth_id
        ])->exists();

        return response()
            ->json([
                'status' => 101,
                'message' => 'User Retrieved',
                'total_favorites' => Favorite::where('user_id', $id)->count(),
                'total_reviews' => Review::where('user_id', $id)->count(),
                'total_watchlists' => Watchlist::where('user_id', $id)->count(),
                'total_followings' => Following::where('user_id', $id)->count(),
                'total_followers' => Following::where('following_id', $id)->count(),
                'follows_me' => $follows_me,
                'rate_05' => Review::where('user_id', $id)->where('rating', 0.5)->count(),
                'rate_10' => Review::where('user_id', $id)->where('rating', 1)->count(),
                'rate_15' => Review::where('user_id', $id)->where('rating', 1.5)->count(),
                'rate_20' => Review::where('user_id', $id)->where('rating', 2)->count(),
                'rate_25' => Review::where('user_id', $id)->where('rating', 2.5)->count(),
                'rate_30' => Review::where('user_id', $id)->where('rating', 3)->count(),
                'rate_35' => Review::where('user_id', $id)->where('rating', 3.5)->count(),
                'rate_40.' => Review::where('user_id', $id)->where('rating', 4)->count(),
                'rate_45' => Review::where('user_id', $id)->where('rating', 4.5)->count(),
                'rate_50' => Review::where('user_id', $id)->where('rating', 5)->count(),
                'result' => $user
            ]);
    }

    public function signin(Request $request) {
        $username = $request['username'];
        $password = HASH('SHA256', $request['password']);
        $token = HASH('SHA256', Str::random(100));

        $auth = User::where([
            'username' => $username,
            'password' => $password
        ])->exists();

        if ($auth == true) {
            User::where([
                'username' => $username
            ])->update([
                'token' => $token
            ]);

            $user = User::select('id', 'token')
                ->firstWhere([
                    'username' => $username
                ]);

            return response()
                ->json([
                    'status' => 101,
                    'message' => 'Signed In',
                    'result' => $user
                ]);
        } else {
            return response()
                ->json([
                    'status' => 505,
                    'message' => 'Invalid Credentials'
                ]);
        }
    }

    public function signup(Request $request) {
        $full_name = $request['first_name'] . '+' . $request['last_name'];
        $full_name = preg_replace('/\s+/', '+', $full_name);
        $profile_picture = 'https://ui-avatars.com/api/?name=' . $full_name;
        $password = HASH('SHA256', $request['password']);
        $token = HASH('SHA256', Str::random(100));

        $user = User::create([
            'first_name' => $request['first_name'],
            'last_name' => $request['last_name'],
            'username' => $request['username'],
            'email' => $request['email'],
            'profile_picture' => $profile_picture,
            'password' => $password,
            'token' => $token
        ]);

        return response()
            ->json([
                'status' => 202,
                'message' => 'User Created',
                'result' => $user
            ]);
    }

    public function signout(Request $request, $id) {
        $user_id = User::select('id')
            ->where([
                'id' => $id
            ])->firstOrFail();
        
        $auth = User::where([
            'id' => $user_id['id'],
            'token' => $request['user_token']
        ])->exists();
        
        if ($auth == true) {
            User::findOrFail($id)
                ->update([
                    'token' => null
                ]);
            
            return response()
                ->json([
                    'status' => 101,
                    'message' => 'Signed Out'
                ]);
        } else {
            return response()
                ->json([
                    'status' => 505,
                    'message' => 'Invalid Credentials'
                ]);
        }
    }

    public function update(Request $request, $id) {
        $user_id = User::select('id')
            ->where([
                'id' => $id
            ])->firstOrFail();
        
        $auth = User::where([
            'id' => $user_id['id'],
            'token' => $request['user_token']
        ])->exists();
        
        if ($auth == true) {
            $full_name = $request['first_name'] . '+' . $request['last_name'];
            $full_name = preg_replace('/\s+/', '+', $full_name);
            $profile_picture = 'https://ui-avatars.com/api/?name=' . $full_name;
            $password = HASH('SHA256', $request['password']);
            
            User::findOrFail($id)
                ->update([
                    'first_name' => $request['first_name'],
                    'last_name' => $request['last_name'],
                    'username' => $request['username'],
                    'email' => $request['email'],
                    'profile_picture' => $profile_picture,
                    'password' => $password
                ]);
                
            return response()
                ->json([
                    'status' => 303,
                    'message' => 'User Updated'
                ]);
        } else {
            return response()
                ->json([
                    'status' => 505,
                    'message' => 'Invalid Credentials'
                ]);
        }
    }

    public function delete(Request $request, $id) {
        $user_id = User::select('id')
            ->where([
                'id' => $id
            ])->firstOrFail();
        
        $auth = User::where([
            'id' => $user_id['id'],
            'token' => $request['user_token']
        ])->exists();
        
        if ($auth == true) {
            User::findOrFail($id)
                ->delete();
            
            return response()
                ->json([
                    'status' => 303,
                    'message' => 'User Deleted'
                ]);
        } else {
            return response()
                ->json([
                    'status' => 505,
                    'message' => 'Invalid Credentials'
                ]);
        }
    }
}
