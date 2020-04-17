<?php

namespace App\Http\Controllers;

use App\Favorite;
use App\Following;
use App\Review;
use App\User;
use App\Watchlist;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

//use Illuminate\Support\Collection;

class UserController extends Controller
{
    public function self(Request $request) {
        $auth_uid = $request->header('auth_uid');
        $auth_token = $request->header('auth_token');

        $auth = User::where([
            'id' => $auth_uid,
            'token' => $auth_token
        ])->exists();
        
        if ($auth == true) {
            $self = User::findOrFail($auth_uid);
            
            return response()
                ->json([
                    'status' => 211,
                    'message' => 'Self Retrieved',
                    'result' => $self
                ]);
        } else {
            return response()
                ->json([
                    'status' => 808,
                    'message' => 'Invalid Credentials'
                ]);
        }
    }

    public function show(Request $request, $id) {
        $auth_uid = $request->header('auth_uid');

        $user = User::select('id', 'first_name', 'last_name', 'profile_picture')
            ->findOrFail($id);

        $follows_me = Following::where([
            'user_id' => $id,
            'following_id' => $auth_uid
        ])->exists();

        $favorites = Favorite::with([
            'film'
        ])->where('user_id', $id)
          ->orderBy('created_at', 'desc')
          ->take(5)
          ->get();

        $reviews = Review::with([
            'film'
        ])->where('user_id', $id)
          ->orderBy('created_at', 'desc')
          ->take(5)
          ->get();

        $metadata = collect([
            'follows_me' => $follows_me,
            'favorites' => Favorite::where('user_id', $id)->count(),
            'reviews' => Review::where('user_id', $id)->count(),
            'watchlists' => Watchlist::where('user_id', $id)->count(),
            'followings' => Following::where('user_id', $id)->count(),
            'followers' => Following::where('following_id', $id)->count(),
            'rate_0.5' => Review::where('user_id', $id)->where('rating', 0.5)->count(),
            'rate_1.0' => Review::where('user_id', $id)->where('rating', 1)->count(),
            'rate_1.5' => Review::where('user_id', $id)->where('rating', 1.5)->count(),
            'rate_2.0' => Review::where('user_id', $id)->where('rating', 2)->count(),
            'rate_2.5' => Review::where('user_id', $id)->where('rating', 2.5)->count(),
            'rate_3.0' => Review::where('user_id', $id)->where('rating', 3)->count(),
            'rate_3.5' => Review::where('user_id', $id)->where('rating', 3.5)->count(),
            'rate_4.0.' => Review::where('user_id', $id)->where('rating', 4)->count(),
            'rate_4.5' => Review::where('user_id', $id)->where('rating', 4.5)->count(),
            'rate_5.0' => Review::where('user_id', $id)->where('rating', 5)->count()
        ]);

        $recent = collect([
            'favorites' => isset($favorites[0]) ? $favorites : null,
            'reviews' => isset($reviews[0]) ? $reviews : null
        ]);

        $result = collect([
            'user' => $user,
            'metadata' => $metadata,
            'recent' => $recent
        ]);

        return response()
            ->json([
                'status' => 201,
                'message' => 'User Retrieved',
                'result' => $result
            ]);
    }

    public function signin(Request $request) {
        $this->validate($request, [
            'username' => 'required|string',
            'password' => 'required|string'
        ],[
            'required' => 'Input field harus di isi.',
            'string' => 'Format input field harus berupa string.'
        ]);

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
                    'status' => 212,
                    'message' => 'User Signed In',
                    'result' => $user
                ]);
        } else {
            return response()
                ->json([
                    'status' => 808,
                    'message' => 'Invalid Credentials'
                ]);
        }
    }

    public function signup(Request $request) {
        $this->validate($request, [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'username' => 'required|string|min:5|max:255|unique:users',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|string|min:8|max:255'
        ],[
            'required' => 'Input field harus di isi.',
            'string' => 'Format input field harus berupa string.',
            'email' => 'Format email salah.',
            'username.min' => 'Username minimal 5 karakter.',
            'password.min' => 'Password minimal 8 karakter.',
            'max' => 'Input yang dimasukkan melebihi batas.',
            'username.unique' => 'Username tersebut sudah digunakan.',
            'email.unique' => 'Email tersebut sudah digunakan.'
        ]);

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
                'message' => 'User Signed Up',
                'result' => $user
            ]);
    }

    public function signout(Request $request) {
        $auth_uid = $request->header('auth_uid');
        $auth_token = $request->header('auth_token');
        
        $auth = User::where([
            'id' => $auth_uid,
            'token' => $auth_token
        ])->exists();
        
        if ($auth == true) {
            User::findOrFail($auth_uid)
                ->update([
                    'token' => null
                ]);
            
            return response()
                ->json([
                    'status' => 232,
                    'message' => 'User Signed Out'
                ]);
        } else {
            return response()
                ->json([
                    'status' => 808,
                    'message' => 'Invalid Credentials'
                ]);
        }
    }

    public function update(Request $request, $id) {
        $auth_token = $request->header('auth_token');

        $auth = User::where([
            'id' => $id,
            'token' => $auth_token
        ])->exists();
        
        if ($auth == true) {
            $this->validate($request, [
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'username' => 'required|string|min:5|max:255|unique:users',
                'email' => 'required|email|max:255|unique:users',
                'password' => 'required|string|min:8|max:255'
            ],[
                'required' => 'Input field harus di isi.',
                'string' => 'Format input field harus berupa string.',
                'email' => 'Format email salah.',
                'username.min' => 'Username minimal 5 karakter.',
                'password.min' => 'Password minimal 8 karakter.',
                'max' => 'Input yang dimasukkan melebihi batas.',
                'username.unique' => 'Username tersebut sudah digunakan.',
                'email.unique' => 'Email tersebut sudah digunakan.'
            ]);

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
                    'status' => 203,
                    'message' => 'User Updated'
                ]);
        } else {
            return response()
                ->json([
                    'status' => 808,
                    'message' => 'Invalid Credentials'
                ]);
        }
    }

    public function delete(Request $request, $id) {
        $auth_token = $request->header('auth_token');

        $auth = User::where([
            'id' => $id,
            'token' => $auth_token
        ])->exists();
        
        if ($auth == true) {
            User::findOrFail($id)
                ->delete();
            
            return response()
                ->json([
                    'status' => 204,
                    'message' => 'User Deleted'
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
