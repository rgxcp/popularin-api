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

    public function showFilmFavoritesFromAll($tmdb_id) {
        $favorites = Favorite::with([
            'user'
        ])->where('tmdb_id', $tmdb_id)
          ->orderBy('created_at', 'desc')
          ->paginate(50);
        
        return response()->json([
            'status' => isset($favorites[0]) ? 101 : 606,
            'message' => isset($favorites[0]) ? 'Request Retrieved' : 'Request Not Found',
            'result' => isset($favorites[0]) ? $favorites : null
        ]);
    }

    public function showFilmFavoritesFromFollowing(Request $request, $tmdb_id) {
        $authID = $request->header('Auth-ID');

        $followings = Following::select('following_id')->where('user_id', $authID);

        $favorites = Favorite::with([
            'user'
        ])->where('tmdb_id', $tmdb_id)
          ->whereIn('user_id', $followings)
          ->orderBy('created_at', 'desc')
          ->paginate(50);
        
        return response()->json([
            'status' => isset($favorites[0]) ? 101 : 606,
            'message' => isset($favorites[0]) ? 'Request Retrieved' : 'Request Not Found',
            'result' => isset($favorites[0]) ? $favorites : null
        ]);
    }

    public function showUserFavorites($user_id) {
        $favorites = Favorite::with([
            'film'
        ])->where('user_id', $user_id)
          ->orderBy('created_at', 'desc')
          ->paginate(50);
        
        return response()->json([
            'status' => isset($favorites[0]) ? 101 : 606,
            'message' => isset($favorites[0]) ? 'Request Retrieved' : 'Request Not Found',
            'result' => isset($favorites[0]) ? $favorites : null
        ]);
    }

    public function create(Request $request) {
        $authID = $request->header('Auth-ID');
        $authToken = $request->header('Auth-Token');
        
        $isAuth = User::where([
            'id' => $authID,
            'token' => $authToken
        ])->exists();
        
        if ($isAuth) {
            $tmdb_id = $request['tmdb_id'];

            $inFavorite = Favorite::where([
                'user_id' => $authID,
                'tmdb_id' => $tmdb_id
            ])->exists();

            if ($inFavorite) {
                return response()->json([
                    'status' => 646,
                    'message' => 'Already Favorited'
                ]);
            } else {
                $validator = Validator::make($request->all(), [
                    'tmdb_id' => 'required|numeric'
                ],[
                    'required' => 'TMDb ID harus di isi',
                    'numeric' => 'Format TMDb ID harus berupa numerik'
                ]);
        
                if ($validator->fails()) {
                    return response()->json([
                        'status' => 626,
                        'message' => 'Validator Fails',
                        'result' => $validator->errors()->all()
                    ]);
                } else {
                    $filmExist = Film::where('tmdb_id', $tmdb_id)->exists();

                    if (!$filmExist) {
                        $this->addFilm($tmdb_id);
                    }
                    
                    Favorite::create([
                        'user_id' => $authID,
                        'tmdb_id' => $tmdb_id
                    ]);
                    
                    return response()->json([
                        'status' => 202,
                        'message' => 'Request Created'
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

    public function delete(Request $request, $tmdb_id) {
        $authID = $request->header('Auth-ID');
        $authToken = $request->header('Auth-Token');
        
        $isAuth = User::where([
            'id' => $authID,
            'token' => $authToken
        ])->exists();
        
        if ($isAuth) {
            Favorite::where([
                'user_id' => $authID,
                'tmdb_id' => $tmdb_id
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
