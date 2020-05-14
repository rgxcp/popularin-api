<?php

namespace App\Http\Controllers;

use App\Favorite;
use App\Film;
use App\Following;
use App\User;
use App\Http\Traits\FilmTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FavoriteController extends Controller
{
    use FilmTrait;

    public function showFavoritesFromAll($tmdb_id) {
        $favorites = Favorite::with([
            'user'
        ])->where('tmdb_id', $tmdb_id)
          ->orderBy('created_at', 'desc')
          ->paginate(30);
        
        return response()->json([
            'status' => isset($favorites[0]) ? 101 : 606,
            'message' => isset($favorites[0]) ? 'Request Retrieved' : 'Request Not Found',
            'result' => isset($favorites[0]) ? $favorites : null
        ]);
    }

    public function showFavoritesFromFollowing(Request $request, $tmdb_id) {
        $auth_uid = $request->header('auth_uid');

        $followings = Following::select('following_id')->where('user_id', $auth_uid);

        $favorites = Favorite::with([
            'user'
        ])->where('tmdb_id', $tmdb_id)
          ->whereIn('user_id', $followings)
          ->orderBy('created_at', 'desc')
          ->paginate(30);
        
        return response()->json([
            'status' => isset($favorites[0]) ? 101 : 606,
            'message' => isset($favorites[0]) ? 'Request Retrieved' : 'Request Not Found',
            'result' => isset($favorites[0]) ? $favorites : null
        ]);
    }

    public function shows($user_id) {
        $favorites = Favorite::with([
            'film'
        ])->where('user_id', $user_id)
          ->orderBy('created_at', 'desc')
          ->paginate(30);
        
        return response()->json([
            'status' => isset($favorites[0]) ? 101 : 606,
            'message' => isset($favorites[0]) ? 'Request Retrieved' : 'Request Not Found',
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
        
        if ($auth) {
            $tmdb_id = $request['tmdb_id'];

            $in_favorite = Favorite::where([
                'user_id' => $auth_uid,
                'tmdb_id' => $tmdb_id
            ])->exists();

            if ($in_favorite) {
                return response()->json([
                    'status' => 646,
                    'message' => 'Already Favorited'
                ]);
            } else {
                $validator = Validator::make($request->all(), [
                    'tmdb_id' => 'required|integer'
                ]);
        
                if ($validator->fails()) {
                    return response()->json([
                        'status' => 626,
                        'message' => 'Validator Fails',
                        'result' => $validator->errors()->all()
                    ]);
                } else {
                    $film_exist = Film::where('tmdb_id', $tmdb_id)->exists();

                    if (!$film_exist) {
                        $film_exist = $this->addFilm($tmdb_id);
                    }
                    
                    Favorite::create([
                        'user_id' => $auth_uid,
                        'tmdb_id' => $tmdb_id
                    ]);
                    
                    return response()->json([
                        'status' => 202,
                        'message' => 'Request Created',
                        'film_exist' => $film_exist
                    ]);
                }
            }
        } else {
            return response()->json([
                'status' => 616,
                'message' => 'Invalid Credentials'
            ]);
        }
    }

    public function delete(Request $request, $film_id) {
        $auth_uid = $request->header('auth_uid');
        $auth_token = $request->header('auth_token');
        
        $auth = User::where([
            'id' => $auth_uid,
            'token' => $auth_token
        ])->exists();
        
        if ($auth) {
            Favorite::where([
                'user_id' => $auth_uid,
                'tmdb_id' => $film_id
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
