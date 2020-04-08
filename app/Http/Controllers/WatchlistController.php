<?php

namespace App\Http\Controllers;

use App\Film;
use App\User;
use App\Watchlist;
use Illuminate\Http\Request;

class WatchlistController extends Controller
{
    public function shows(Request $request, $user_id) {
        $has_watchlists = Watchlist::where([
            'user_id' => $request['user_id']
        ])->exists();

        if ($has_watchlists == true) {
            $watchlists = Watchlist::with([
                    'film'
                ])->where([
                    'user_id' => $request['user_id']
                ])->orderBy('created_at', 'desc')
                  ->paginate(30);
            
            return response()
                ->json([
                    'status' => 101,
                    'message' => 'Watchlists Retrieved',
                    'results' => $watchlists
                ]);
        } else {
            return response()
                ->json([
                    'status' => 606,
                    'message' => 'Watchlists Not Found'
                ]);
        }
    }

    public function create(Request $request) {
        $auth = User::where([
            'id' => $request['user_id'],
            'token' => $request['user_token']
        ])->exists();
        
        if ($auth == true) {
            Watchlist::create([
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
                    'message' => 'Watchlist Added',
                    'film_exist' => $film_exist
                ]);
        } else {
            return response()
                ->json([
                    'status' => 505,
                    'message' => 'Not Authorized to Add Watchlist'
                ]);
        }
    }

    public function delete(Request $request, $id) {
        $user_id = Watchlist::select('user_id')
            ->where([
                'id' => $id
            ])->firstOrFail();
        
        $auth = User::where([
            'id' => $user_id['user_id'],
            'token' => $request['user_token']
        ])->exists();
        
        if ($auth == true) {
            Watchlist::findOrFail($id)
                ->delete();
            
            return response()
                ->json([
                    'status' => 404,
                    'message' => 'Watchlist Deleted'
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
