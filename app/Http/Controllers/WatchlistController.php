<?php

namespace App\Http\Controllers;

use App\Film;
use App\User;
use App\Watchlist;
use Illuminate\Http\Request;

class WatchlistController extends Controller
{
    public function shows(Request $request, $user_id) {
        $watchlists = Watchlist::with([
            'film'
        ])->where('user_id', $user_id)
          ->orderBy('created_at', 'desc')
          ->paginate(30);
        
        return response()
            ->json([
                'status' => isset($watchlists[0]) ? 601 : 969,
                'message' => isset($watchlists[0]) ? 'Watchlists Retrieved' : 'Empty Watchlists',
                'results' => isset($watchlists[0]) ? $watchlists : null
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
            $in_watchlist = Watchlist::where([
                'user_id' => $auth_uid,
                'tmdb_id' => $request['tmdb_id']
            ])->exists();

            if ($in_watchlist == true) {
                return response()
                    ->json([
                        'status' => 926,
                        'message' => 'Already in Watchlist'
                    ]);
            } else {
                $this->validate($request, [
                    'tmdb_id' => 'required|integer'
                ],[
                    'required' => 'Input field harus di isi.',
                    'integer' => 'Format input field harus berupa integer.'
                ]);
    
                Watchlist::create([
                    'user_id' => $auth_uid,
                    'tmdb_id' => $request['tmdb_id']
                ]);
    
                $film_exist = Film::where([
                    'tmdb_id' => $request['tmdb_id']
                ])->exists();
                
                return response()
                    ->json([
                        'status' => 602,
                        'message' => 'Watchlist Added',
                        'film_exist' => $film_exist
                    ]);
            }
        } else {
            return response()
                ->json([
                    'status' => 808,
                    'message' => 'Invalid Credentials'
                ]);
        }
    }

    public function delete(Request $request, $id) {
        $auth_uid = Watchlist::select('user_id')->where('id', $id)->firstOrFail();
        $auth_token = $request->header('auth_token');
        
        $auth = User::where([
            'id' => $auth_uid['user_id'],
            'token' => $auth_token
        ])->exists();
        
        if ($auth == true) {
            Watchlist::findOrFail($id)
                ->delete();
            
            return response()
                ->json([
                    'status' => 604,
                    'message' => 'Watchlist Removed'
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
