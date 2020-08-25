<?php

namespace App\Http\Controllers;

use App\Favorite;
use App\Film;
use App\Following;
use App\Http\Traits\FilmTrait;
use App\Http\Traits\PointTrait;
use App\Point;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    use FilmTrait;
    use PointTrait;

    public function showsFilmFavoriteFromAll($tmdbID)
    {
        $favorites = Favorite::with([
            'user'
        ])->where('tmdb_id', $tmdbID)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'status' => isset($favorites[0]) ? 101 : 606,
            'message' => isset($favorites[0]) ? 'Request Retrieved' : 'Request Not Found',
            'result' => isset($favorites[0]) ? $favorites : null
        ]);
    }

    public function showsFilmFavoriteFromFollowing($tmdbID)
    {
        $followings = Following::select('following_id')->where('user_id', Auth::id());

        $favorites = Favorite::with([
            'user'
        ])->where('tmdb_id', $tmdbID)
            ->whereIn('user_id', $followings)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'status' => isset($favorites[0]) ? 101 : 606,
            'message' => isset($favorites[0]) ? 'Request Retrieved' : 'Request Not Found',
            'result' => isset($favorites[0]) ? $favorites : null
        ]);
    }

    public function showsUserFavorite($userID)
    {
        $favorites = Favorite::with([
            'film'
        ])->where('user_id', $userID)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'status' => isset($favorites[0]) ? 101 : 606,
            'message' => isset($favorites[0]) ? 'Request Retrieved' : 'Request Not Found',
            'result' => isset($favorites[0]) ? $favorites : null
        ]);
    }

    public function create($tmdbID)
    {
        $authID = Auth::id();

        $inFavorite = Favorite::where([
            'user_id' => $authID,
            'tmdb_id' => $tmdbID
        ])->exists();

        if ($inFavorite) {
            return response()->json([
                'status' => 646,
                'message' => 'Already Favorited'
            ]);
        } else {
            $filmExist = Film::where('tmdb_id', $tmdbID)->exists();

            if (!$filmExist) {
                $this->addFilm($tmdbID);
            }

            Favorite::create([
                'user_id' => $authID,
                'tmdb_id' => $tmdbID
            ]);

            $pointExist = Point::where([
                'user_id' => $authID,
                'type_id' => $tmdbID,
                'type' => 'FAVORITE'
            ])->exists();

            if (!$pointExist) {
                $this->addPoint($authID, $tmdbID, 10, 'FAVORITE');
            }

            return response()->json([
                'status' => 202,
                'message' => 'Request Created'
            ]);
        }
    }

    public function delete($tmdbID)
    {
        Favorite::where([
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
