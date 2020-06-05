<?php

namespace App\Http\Controllers;

use Auth;
use App\Film;
use App\Following;
use App\Watchlist;
use App\Http\Traits\FilmTrait;

class WatchlistController extends Controller
{
    use FilmTrait;

    public function showFilmWatchlistsFromAll($tmdb_id) {
        $watchlists = Watchlist::with([
            'user'
        ])->where('tmdb_id', $tmdb_id)
          ->orderBy('created_at', 'desc')
          ->paginate(50);
        
        return response()->json([
            'status' => isset($watchlists[0]) ? 101 : 606,
            'message' => isset($watchlists[0]) ? 'Request Retrieved' : 'Request Not Found',
            'result' => isset($watchlists[0]) ? $watchlists : null
        ]);
    }

    public function showFilmWatchlistsFromFollowing($tmdb_id) {
        $authID = Auth::user()->id;

        $followings = Following::select('following_id')->where('user_id', $authID);

        $watchlists = Watchlist::with([
            'user'
        ])->where('tmdb_id', $tmdb_id)
          ->whereIn('user_id', $followings)
          ->orderBy('created_at', 'desc')
          ->paginate(50);
        
        return response()->json([
            'status' => isset($watchlists[0]) ? 101 : 606,
            'message' => isset($watchlists[0]) ? 'Request Retrieved' : 'Request Not Found',
            'result' => isset($watchlists[0]) ? $watchlists : null
        ]);
    }

    public function showUserWatchlists($user_id) {
        $watchlists = Watchlist::with([
            'film'
        ])->where('user_id', $user_id)
          ->orderBy('created_at', 'desc')
          ->paginate(50);
        
        return response()->json([
            'status' => isset($watchlists[0]) ? 101 : 606,
            'message' => isset($watchlists[0]) ? 'Request Retrieved' : 'Request Not Found',
            'result' => isset($watchlists[0]) ? $watchlists : null
        ]);
    }

    public function create($tmdb_id) {
        $authID = Auth::user()->id;
        
        $inWatchlist = Watchlist::where([
            'user_id' => $authID,
            'tmdb_id' => $tmdb_id
        ])->exists();

        if ($inWatchlist) {
            return response()->json([
                'status' => 676,
                'message' => 'Already Watchlisted'
            ]);
        } else {
            $filmExist = Film::where('tmdb_id', $tmdb_id)->exists();

            if (!$filmExist) {
                $this->addFilm($tmdb_id);
            }
            
            Watchlist::create([
                'user_id' => $authID,
                'tmdb_id' => $tmdb_id
            ]);
            
            return response()->json([
                'status' => 202,
                'message' => 'Request Created'
            ]);
        }
    }

    public function delete($tmdb_id) {
        $authID = Auth::user()->id;
        
        Watchlist::where([
            'user_id' => $authID,
            'tmdb_id' => $tmdb_id
        ])->firstOrFail()
          ->delete();
        
        return response()->json([
            'status' => 404,
            'message' => 'Request Deleted'
        ]);
    }
}
