<?php

namespace App\Http\Controllers;

use App\Film;
use App\Following;
use App\Http\Traits\FilmTrait;
use App\Http\Traits\PointTrait;
use App\Point;
use App\Watchlist;
use Illuminate\Support\Facades\Auth;

class WatchlistController extends Controller
{
    use FilmTrait;
    use PointTrait;

    public function showsFilmWatchlistFromAll($tmdbID)
    {
        $watchlists = Watchlist::with([
            'user'
        ])->where('tmdb_id', $tmdbID)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'status' => isset($watchlists[0]) ? 101 : 606,
            'message' => isset($watchlists[0]) ? 'Request Retrieved' : 'Request Not Found',
            'result' => isset($watchlists[0]) ? $watchlists : null
        ]);
    }

    public function showsFilmWatchlistFromFollowing($tmdbID)
    {
        $followings = Following::select('following_id')->where('user_id', Auth::id());

        $watchlists = Watchlist::with([
            'user'
        ])->where('tmdb_id', $tmdbID)
            ->whereIn('user_id', $followings)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'status' => isset($watchlists[0]) ? 101 : 606,
            'message' => isset($watchlists[0]) ? 'Request Retrieved' : 'Request Not Found',
            'result' => isset($watchlists[0]) ? $watchlists : null
        ]);
    }

    public function showsUserWatchlist($userID)
    {
        $watchlists = Watchlist::with([
            'film'
        ])->where('user_id', $userID)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'status' => isset($watchlists[0]) ? 101 : 606,
            'message' => isset($watchlists[0]) ? 'Request Retrieved' : 'Request Not Found',
            'result' => isset($watchlists[0]) ? $watchlists : null
        ]);
    }

    public function create($tmdbID)
    {
        $authID = Auth::id();

        $inWatchlist = Watchlist::where([
            'user_id' => $authID,
            'tmdb_id' => $tmdbID
        ])->exists();

        if ($inWatchlist) {
            return response()->json([
                'status' => 676,
                'message' => 'Already Watchlisted'
            ]);
        } else {
            $filmExist = Film::where('tmdb_id', $tmdbID)->exists();

            if (!$filmExist) {
                $this->addFilm($tmdbID);
            }

            Watchlist::create([
                'user_id' => $authID,
                'tmdb_id' => $tmdbID
            ]);

            $pointExist = Point::where([
                'user_id' => $authID,
                'type_id' => $tmdbID,
                'type' => 'WATCHLIST'
            ])->exists();

            if (!$pointExist) {
                $this->addPoint($authID, $tmdbID, 5, 'WATCHLIST');
            }

            return response()->json([
                'status' => 202,
                'message' => 'Request Created'
            ]);
        }
    }

    public function delete($tmdbID)
    {
        Watchlist::where([
            'user_id' => Auth::id(),
            'tmdb_id' => $tmdbID
        ])->firstOrFail()
            ->delete();

        return response()->json([
            'status' => 404,
            'message' => 'Request Deleted'
        ]);
    }
}
