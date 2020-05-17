<?php

namespace App\Http\Controllers;

use App\Favorite;
use App\Following;
use App\Review;
use App\User;
use App\Watchlist;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function search(Request $request, $query) {
        $users = User::select(
            'id',
            'full_name',
            'username',
            'profile_picture'
        )->where('first_name', 'like', $query)
         ->orWhere('last_name', 'like', $query)
         ->orWhere('full_name', 'like', $query)
         ->orWhere('username', 'like', $query)
         ->orderBy('created_at', 'desc')
         ->paginate(50);

        return response()->json([
            'status' => 101,
            'message' => 'Request Retrieved',
            'result' => $users
        ]);
    }

    public function self(Request $request) {
        $auth_uid = $request->header('auth_uid');
        $auth_token = $request->header('auth_token');

        $isAuth = User::where([
            'id' => $auth_uid,
            'token' => $auth_token
        ])->exists();
        
        if ($isAuth) {
            $self = User::findOrFail($auth_uid);
            
            return response()->json([
                'status' => 101,
                'message' => 'Request Retrieved',
                'result' => $self
            ]);
        } else {
            return response()->json([
                'status' => 616,
                'message' => 'Invalid Credentials'
            ]);
        }
    }

    public function show(Request $request, $id) {
        Carbon::setLocale('id');

        $auth_uid = $request->header('auth_uid');

        $user = User::select(
            'id',
            'first_name',
            'last_name',
            'full_name',
            'username',
            'profile_picture',
            'created_at'
        )->findOrFail($id);

        $recent_favorites = Favorite::with([
            'film'
        ])->where('user_id', $id)
          ->orderBy('created_at', 'desc')
          ->take(5)
          ->get();

        $recent_reviews = Review::with([
            'film'
        ])->where('user_id', $id)
          ->orderBy('created_at', 'desc')
          ->take(5)
          ->get();

        $metadata = collect([
            'joined_since' => $user->created_at->diffForHumans(),
            'is_following' => Following::where('user_id', $auth_uid)->where('following_id', $id)->exists(),
            'is_follower' => Following::where('user_id', $id)->where('following_id', $auth_uid)->exists(),
            'total_following' => Following::where('user_id', $id)->count(),
            'total_follower' => Following::where('following_id', $id)->count(),
            'total_favorite' => Favorite::where('user_id', $id)->count(),
            'total_review' => Review::where('user_id', $id)->count(),
            'total_watchlist' => Watchlist::where('user_id', $id)->count()
            /*
            'total_rate_05' => Review::where(['user_id' => $id, 'rating' => 0.5])->count(),
            'total_rate_10' => Review::where(['user_id' => $id, 'rating' => 1.0])->count(),
            'total_rate_15' => Review::where(['user_id' => $id, 'rating' => 1.5])->count(),
            'total_rate_20' => Review::where(['user_id' => $id, 'rating' => 2.0])->count(),
            'total_rate_25' => Review::where(['user_id' => $id, 'rating' => 2.5])->count(),
            'total_rate_30' => Review::where(['user_id' => $id, 'rating' => 3.0])->count(),
            'total_rate_35' => Review::where(['user_id' => $id, 'rating' => 3.5])->count(),
            'total_rate_40' => Review::where(['user_id' => $id, 'rating' => 4.0])->count(),
            'total_rate_45' => Review::where(['user_id' => $id, 'rating' => 4.5])->count(),
            'total_rate_50' => Review::where(['user_id' => $id, 'rating' => 5.0])->count()
            */
        ]);

        $activity = collect([
            'recent_favorites' => isset($recent_favorites[0]) ? $recent_favorites : null,
            'recent_reviews' => isset($recent_reviews[0]) ? $recent_reviews : null
        ]);

        $collection = collect([
            'user' => $user,
            'metadata' => $metadata,
            'activity' => $activity
        ]);

        return response()->json([
            'status' => 101,
            'message' => 'Request Retrieved',
            'result' => $collection
        ]);
    }

    public function signup(Request $request) {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required',
            'last_name' => 'required',
            'username' => 'required|alpha_dash|min:5|unique:users,username',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|different:username'
        ],[
            'first_name.required' => 'Nama depan harus di isi',
            'last_name.required' => 'Nama belakang harus di isi',
            'username.required' => 'Username harus di isi',
            'email.required' => 'Alamat email harus di isi',
            'password.required' => 'Kata sandi harus di isi',
            'alpha_dash' => 'Username tidak bisa mengandung spasi',
            'email' => 'Format alamat email tidak sesuai',
            'username.min' => 'Username minimal 5 karakter',
            'password.min' => 'Kata sandi minimal 8 karakter',
            'username.unique' => 'Username tersebut sudah digunakan',
            'email.unique' => 'Alamat email tersebut sudah digunakan',
            'different' => 'Kata sandi dan username harus berbeda'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 626,
                'message' => 'Validator Fails',
                'result' => $validator->errors()->all()
            ]);
        } else {
            $full_name = $request['first_name'].' '.$request['last_name'];
            $profile_picture = 'https://ui-avatars.com/api/?name='.preg_replace('/\s+/', '+', $full_name).'&size=512';
    
            $user = User::create([
                'first_name' => $request['first_name'],
                'last_name' => $request['last_name'],
                'full_name' => $full_name,
                'username' => $request['username'],
                'email' => $request['email'],
                'profile_picture' => $profile_picture,
                'password' => Hash::make($request['password']),
                'token' => Str::random(100)
            ]);
    
            return response()->json([
                'status' => 505,
                'message' => 'User Signed Up',
                'result' => $user
            ]);
        }
    }

    public function signin(Request $request) {
        $validator = Validator::make($request->all(), [
            'username' => 'required',
            'password' => 'required'
        ],[
            'username.required' => 'Username harus di isi',
            'password.required' => 'Kata sandi harus di isi'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 626,
                'message' => 'Validator Fails',
                'result' => $validator->errors()->all()
            ]);
        } else {
            $user = User::select(
                'id',
                'token',
                'password'
            )->where('username', $request['username'])
             ->firstOrFail();

            $isAuth = Hash::check(
                $request['password'],
                $user->password
            );

            if ($isAuth) {
                $user->update([
                    'token' => Str::random(100)
                ]);

                return response()->json([
                    'status' => 515,
                    'message' => 'User Signed In',
                    'result' => $user
                ]);
            } else {
                return response()->json([
                    'status' => 616,
                    'message' => 'Invalid Credentials'
                ]);
            }
        }
    }

    public function signout(Request $request) {
        $auth_uid = $request->header('auth_uid');
        $auth_token = $request->header('auth_token');
        
        $isAuth = User::where([
            'id' => $auth_uid,
            'token' => $auth_token
        ])->exists();
        
        if ($isAuth) {
            User::findOrFail($auth_uid)->update([
                'token' => null
            ]);
            
            return response()->json([
                'status' => 525,
                'message' => 'User Signed Out'
            ]);
        } else {
            return response()->json([
                'status' => 616,
                'message' => 'Invalid Credentials'
            ]);
        }
    }

    public function update(Request $request, $id) {
        $auth_token = $request->header('auth_token');

        $isAuth = User::where([
            'id' => $id,
            'token' => $auth_token
        ])->exists();
        
        if ($isAuth) {
            $validator = Validator::make($request->all(), [
                'first_name' => 'required',
                'last_name' => 'required',
                'username' => 'required|alpha_dash|min:5|unique:users,username,'.$id,
                'email' => 'required|email|unique:users,email,'.$id
            ],[
                'first_name.required' => 'Nama depan harus di isi',
                'last_name.required' => 'Nama belakang harus di isi',
                'username.required' => 'Username harus di isi',
                'email.required' => 'Alamat email harus di isi',
                'alpha_dash' => 'Username tidak bisa mengandung spasi',
                'email' => 'Format alamat email tidak sesuai',
                'min' => 'Username minimal 5 karakter',
                'username.unique' => 'Username tersebut sudah digunakan',
                'email.unique' => 'Alamat email tersebut sudah digunakan'
            ]);
    
            if ($validator->fails()) {
                return response()->json([
                    'status' => 626,
                    'message' => 'Validator Fails',
                    'result' => $validator->errors()->all()
                ]);
            } else {
                $full_name = $request['first_name'].' '.$request['last_name'];
                $profile_picture = 'https://ui-avatars.com/api/?name='.preg_replace('/\s+/', '+', $full_name).'&size=512';
                
                $user = User::findOrFail($id);
                
                $user->update([
                    'first_name' => $request['first_name'],
                    'last_name' => $request['last_name'],
                    'full_name' => $full_name,
                    'username' => $request['username'],
                    'email' => $request['email'],
                    'profile_picture' => $profile_picture,
                    'token' => Str::random(100)
                ]);
                    
                return response()->json([
                    'status' => 303,
                    'message' => 'Request Updated',
                    'result' => $user
                ]);
            }
        } else {
            return response()->json([
                'status' => 616,
                'message' => 'Invalid Credentials'
            ]);
        }
    }

    public function updatePassword(Request $request, $id) {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'new_password' => 'required|min:8|different:current_password',
            'confirm_password' => 'required|same:new_password'
        ],[
            'current_password.required' => 'Kata sandi lama harus di isi',
            'new_password.required' => 'Kata sandi baru harus di isi',
            'confirm_password.required' => 'Konfirmasi kata sandi harus di isi',
            'min' => 'Kata sandi baru minimal 8 karakter',
            'different' => 'Kata sandi baru dan lama harus berbeda',
            'same' => 'Kata sandi baru dan konfirmasi tidak sama'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 626,
                'message' => 'Validator Fails',
                'result' => $validator->errors()->all()
            ]);
        } else {
            $user = User::select(
                'id',
                'token',
                'password'
            )->findOrFail($id);

            $isAuth = Hash::check(
                $request['current_password'],
                $user->password
            );

            if ($isAuth) {
                $user->update([
                    'password' => Hash::make($request['confirm_password']),
                    'token' => Str::random(100)
                ]);
                    
                return response()->json([
                    'status' => 303,
                    'message' => 'Request Updated',
                    'result' => $user
                ]);
            } else {
                return response()->json([
                    'status' => 616,
                    'message' => 'Invalid Credentials'
                ]);
            }
        }
    }

    /*
    public function delete(Request $request, $id) {
        $user = User::findOrFail($id);

        $isAuth = Hash::check(
            $request['password'],
            $user->password
        );
        
        if ($isAuth) {
            $user->update([
                'token' => null
            ]);
            
            $user->delete();
            
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
    */
}
