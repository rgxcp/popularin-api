<?php

namespace App\Http\Controllers;

use App\Favorite;
use App\Film;
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
                ])->orderBy('created_at', 'desc')
                  ->paginate(30);
            
            return response()
                ->json([
                    'status' => 101,
                    'message' => 'Favorites Retrieved',
                    'results' => $favorites
                ]);
        } else {
            return response()
                ->json([
                    'status' => 606,
                    'message' => 'Favorites Not Found'
                ]);
        }
    }

    public function create(Request $request) {
        $auth = User::where([
            'id' => $request['user_id'],
            'token' => $request['user_token']
        ])->exists();
        
        if ($auth == true) {
            Favorite::create([
                'user_id' => $request['user_id'],
                'tmdb_id' => $request['tmdb_id']
            ]);

            $film_exist = Film::select('tmdb_id')
                ->where([
                    'tmdb_id' => $request['tmdb_id']
                ])->exists();
            
            return response()
                ->json([
                    'status' => 202,
                    'message' => 'Favorite Added',
                    'film_exist' => $film_exist
                ]);
        } else {
            return response()
                ->json([
                    'status' => 505,
                    'message' => 'Not Authorized to Add Favorite'
                ]);
        }
    }

    public function delete(Request $request, $id) {
        $user_id = Favorite::select('user_id')
            ->where([
                'id' => $id
            ])->firstOrFail();
        
        $auth = User::where([
            'id' => $user_id['user_id'],
            'token' => $request['user_token']
        ])->exists();
        
        if ($auth == true) {
            Favorite::findOrFail($id)
                ->delete();
            
            return response()
                ->json([
                    'status' => 404,
                    'message' => 'Favorite Deleted'
                ]);
        } else {
            return response()
                ->json([
                    'status' => 505,
                    'message' => 'Not Authorized to Delete Favorite'
                ]);
        }
    }
}
