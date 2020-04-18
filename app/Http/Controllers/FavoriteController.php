<?php

namespace App\Http\Controllers;

use App\Favorite;
use App\Film;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FavoriteController extends Controller
{
    public function shows(Request $request, $user_id) {
        $favorites = Favorite::with([
            'film'
        ])->where('user_id', $user_id)
          ->orderBy('created_at', 'desc')
          ->paginate(30);
        
        return response()->json([
            'status' => isset($favorites[0]) ? 501 : 959,
            'message' => isset($favorites[0]) ? 'Favorites Retrieved' : 'Empty Favorites',
            'result' => isset($favorites[0]) ? $favorites : null
        ]);
    }

    public function create(Request $request) {
        $auth_uid = $request->header('auth_uid');
        $auth_token = $request->header('auth_token');
        
        $auth = User::where([
            'id' => $auth_uid,
            'token' => $auth_token
        ])->exists();
        
        if ($auth == true) {
            $in_favorite = Favorite::where([
                'user_id' => $auth_uid,
                'tmdb_id' => $request['tmdb_id']
            ])->exists();

            if ($in_favorite == true) {
                return response()->json([
                    'status' => 925,
                    'message' => 'Already in Favorite'
                ]);
            } else {
                $validator = Validator::make($request->all(), [
                    'tmdb_id' => 'required|integer'
                ]);
        
                if ($validator->fails()) {
                    return response()->json([
                        'status' => 999,
                        'message' => 'Validator Fails',
                        'result' => $validator->errors()->all()
                    ]);
                } else {
                    $film_exist = Film::where('tmdb_id', $request['tmdb_id'])->exists();

                    Favorite::create([
                        'user_id' => $auth_uid,
                        'tmdb_id' => $request['tmdb_id']
                    ]);
                    
                    return response()->json([
                        'status' => 502,
                        'message' => 'Favorite Added',
                        'film_exist' => $film_exist
                    ]);
                }
            }
        } else {
            return response()->json([
                'status' => 808,
                'message' => 'Invalid Credentials'
            ]);
        }
    }

    public function delete(Request $request, $id) {
        $auth_uid = Favorite::select('user_id')->where('id', $id)->firstOrFail();
        $auth_token = $request->header('auth_token');
        
        $auth = User::where([
            'id' => $auth_uid['user_id'],
            'token' => $auth_token
        ])->exists();
        
        if ($auth == true) {
            Favorite::findOrFail($id)->delete();
            
            return response()->json([
                'status' => 504,
                'message' => 'Favorite Removed'
            ]);
        } else {
            return response()->json([
                'status' => 808,
                'message' => 'Invalid Credentials'
            ]);
        }
    }
}
