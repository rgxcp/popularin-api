<?php

namespace App\Http\Controllers;

use App\Film;
use App\User;
use App\Watchlist;
use Illuminate\Http\Request;

class WatchlistController extends Controller{
    public function shows(Request $request, $user_id) {
        $has_watchlists = Watchlist::where([
            'user_id' => $request['user_id']
        ])->exists();

        if ($has_watchlists == true) {
            $watchlists = Watchlist::with([
                    'film'
                ])->where([
                    'user_id' => $request['user_id']
                ])->get();
            
            return response()
                ->json([
                    'status' => 201,
                    'message' => 'Success',
                    'results' => $watchlists
                ]);
        } else {
            return response()
                ->json([
                    'status' => 404,
                    'message' => 'Empty Watchlists'
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
            Watchlist::create($request->all());

            $is_film_exist = Film::select('tmdb_id')
                ->where([
                    'tmdb_id' => $request['tmdb_id']
                ])->exists();
            
            return response()
                ->json([
                    'status' => 201,
                    'is_film_exist' => $is_film_exist,
                    'message' => 'Added to Watchlist'
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
            Watchlist::findOrFail($id)
                ->delete();
            
            return response()
                ->json([
                    'status' => 200,
                    'message' => 'Deleted from Watchlist'
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
