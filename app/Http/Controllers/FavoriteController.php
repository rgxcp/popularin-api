<?php

namespace App\Http\Controllers;

use App\Favorite;
use App\User;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    public function shows(Request $request, $user_id) {
        $has_favorites = Favorite::where([
            'user_id' => $request['user_id']
        ])->exists();

        if ($has_favorites == true) {
            $favorites = Favorite::with([
                    'film'
                ])->where([
                    'user_id' => $request['user_id']
                ])->get();
            
            return response()
                ->json([
                    'status' => 201,
                    'message' => 'Success',
                    'results' => $favorites
                ]);
        } else {
            return response()
                ->json([
                    'status' => 404,
                    'message' => 'Empty Favorites'
                ]);
        }
    }

    public function create(Request $request) {
        $match = User::select('token')
            ->where([
                'id' => $request['user_id'],
                'token' => $request['user_token']
            ])->exists();
        
        if ($match == true) {
            Favorite::create($request->all());

            $is_film_exist = Film::select('tmdb_id')
                ->where([
                    'tmdb_id' => $request['tmdb_id']
                ])->exists();
            
            return response()
                ->json([
                    'status' => 201,
                    'is_film_exist' => $is_film_exist,
                    'message' => 'Added to Favorite'
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
        $match = User::select('token')
            ->where([
                'id' => $request['user_id'],
                'token' => $request['user_token']
            ])->exists();
        
        if ($match == true) {
            Favorite::findOrFail($id)
                ->delete();
            
            return response()
                ->json([
                    'status' => 200,
                    'message' => 'Deleted from Favorite'
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
