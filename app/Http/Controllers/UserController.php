<?php

namespace App\Http\Controllers;

use App\Favorite;
use App\Review;
use App\User;
use App\Watchlist;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function show($id) {
        $user = User::select('id', 'first_name', 'last_name', 'profile_picture')
            ->findOrFail($id);

        return response()
            ->json([
                'status' => 200,
                'message' => 'Success',
                'total_favorites' => Favorite::where('user_id', $id)->count(),
                'total_reviews' => Review::where('user_id', $id)->count(),
                'total_watchlists' => Watchlist::where('user_id', $id)->count(),
                'result' => $user
            ]);
    }

    public function signin(Request $request) {
        $username = $request['username'];
        $password = HASH('SHA256', $request['password']);
        $token = Str::random(50);

        $exist = User::where([
            'username' => $username,
            'password' => $password
        ])->exists();

        if ($exist == true) {
            $auth = User::where([
                'username' => $username
            ])->update([
                'token' => $token
            ]);

            $user = User::select('id', 'token')
                ->firstWhere([
                    'username' => $request['username']
                ]);

            return response()
                ->json([
                    'status' => 200,
                    'message' => 'Signed In',
                    'result' => $user
                ]);
        } else {
            return response()
                ->json([
                    'status' => 403,
                    'message' => 'Invalid Credentials'
                ]);
        }
    }

    public function signup(Request $request) {
        $full_name = $request['first_name'] . '+' . $request['last_name'];
        $full_name = preg_replace('/\s+/', '+', $full_name);
        $profile_picture = 'https://ui-avatars.com/api/?name=' . $full_name;
        $password = HASH('SHA256', $request['password']);
        $token = Str::random(50);

        User::create([
            'first_name' => $request['first_name'],
            'last_name' => $request['last_name'],
            'username' => $request['username'],
            'email' => $request['email'],
            'profile_picture' => $profile_picture,
            'password' => $password,
            'token' => $token
        ]);

        $user = User::select('id', 'token')
                ->firstWhere([
                    'username' => $request['username']
                ]);

        return response()
            ->json([
                'status' => 201,
                'message' => 'Created',
                'result' => $user
            ]);
    }

    public function signout(Request $request, $id) {
        $token = $request['token'];

        $match = User::select('token')
            ->where([
                'id' => $id,
                'token' => $token
            ])->exists();
        
        if ($match == true) {
            User::findOrFail($id)
                ->update([
                    'token' => null
                ]);
            
            return response()
                ->json([
                    'status' => 200,
                    'message' => 'Signed Out'
                ]);
        } else {
            return response()
                ->json([
                    'status' => 403,
                    'message' => 'Invalid Token'
                ]);
        }
    }

    public function update(Request $request, $id) {
        $token = $request['token'];

        $match = User::select('token')
            ->where([
                'id' => $id,
                'token' => $token
            ])->exists();
        
        if ($match == true) {
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
                        'status' => 200,
                        'message' => 'Updated'
                    ]);
        } else {
            return response()
                ->json([
                    'status' => 403,
                    'message' => 'Invalid Token'
                ]);
        }
    }

    public function delete(Request $request, $id) {
        $token = $request['token'];

        $match = User::select('token')
            ->where([
                'id' => $id,
                'token' => $token
            ])->exists();
        
        if ($match == true) {
            User::findOrFail($id)
                ->delete();
            
            return response()
                ->json([
                    'status' => 200,
                    'message' => 'Deleted'
                ]);
        } else {
            return response()
                ->json([
                    'status' => 403,
                    'message' => 'Invalid Token'
                ]);
        }
    }
}
