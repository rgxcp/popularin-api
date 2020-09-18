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

    public function showsFilmWatchlistFromAll($tmdb_id)
    {
        $watchlists = Watchlist::with([
            'user'
        ])->where('tmdb_id', $tmdb_id)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'status' => isset($watchlists[0]) ? 101 : 606,
            'message' => isset($watchlists[0]) ? 'Request Retrieved' : 'Request Not Found',
            'result' => isset($watchlists[0]) ? $watchlists : null
        ]);
    }

    public function showsFilmWatchlistFromFollowing($tmdb_id)
    {
        $followings = Following::select('following_id')->where('user_id', Auth::id());

        $watchlists = Watchlist::with([
            'user'
        ])->where('tmdb_id', $tmdb_id)
            ->whereIn('user_id', $followings)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'status' => isset($watchlists[0]) ? 101 : 606,
            'message' => isset($watchlists[0]) ? 'Request Retrieved' : 'Request Not Found',
            'result' => isset($watchlists[0]) ? $watchlists : null
        ]);
    }

    public function showsUserWatchlist($user_id)
    {
        $watchlists = Watchlist::with([
            'film'
        ])->where('user_id', $user_id)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'status' => isset($watchlists[0]) ? 101 : 606,
            'message' => isset($watchlists[0]) ? 'Request Retrieved' : 'Request Not Found',
            'result' => isset($watchlists[0]) ? $watchlists : null
        ]);
    }

    public function create($tmdb_id)
    {
        $authID = Auth::id();

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

            $pointExist = Point::where([
                'user_id' => $authID,
                'type_id' => $tmdb_id,
                'type' => 'WATCHLIST'
            ])->exists();

            if (!$pointExist) {
                $this->addPoint($authID, $tmdb_id, 5, 'WATCHLIST');
            }

            return response()->json([
                'status' => 202,
                'message' => 'Request Created'
            ]);
        }
    }

    public function delete($tmdb_id)
    {
        Watchlist::where([
            'user_id' => Auth::id(),
            'tmdb_id' => $tmdb_id
        ])->firstOrFail()
            ->delete();

        return response()->json([
            'status' => 404,
            'message' => 'Request Deleted'
        ]);
    }
}
