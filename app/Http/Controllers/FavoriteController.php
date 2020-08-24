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

    public function showsFilmFavoriteFromAll($tmdb_id)
    {
        $favorites = Favorite::with([
            'user'
        ])->where('tmdb_id', $tmdb_id)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'status' => isset($favorites[0]) ? 101 : 606,
            'message' => isset($favorites[0]) ? 'Request Retrieved' : 'Request Not Found',
            'result' => isset($favorites[0]) ? $favorites : null
        ]);
    }

    public function showsFilmFavoriteFromFollowing($tmdb_id)
    {
        $followings = Following::select('following_id')->where('user_id', Auth::id());

        $favorites = Favorite::with([
            'user'
        ])->where('tmdb_id', $tmdb_id)
            ->whereIn('user_id', $followings)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'status' => isset($favorites[0]) ? 101 : 606,
            'message' => isset($favorites[0]) ? 'Request Retrieved' : 'Request Not Found',
            'result' => isset($favorites[0]) ? $favorites : null
        ]);
    }

    public function showsUserFavorite($user_id)
    {
        $favorites = Favorite::with([
            'film'
        ])->where('user_id', $user_id)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'status' => isset($favorites[0]) ? 101 : 606,
            'message' => isset($favorites[0]) ? 'Request Retrieved' : 'Request Not Found',
            'result' => isset($favorites[0]) ? $favorites : null
        ]);
    }

    public function create($tmdb_id)
    {
        $authID = Auth::id();

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
            $filmExist = Film::where('tmdb_id', $tmdb_id)->exists();

            if (!$filmExist) {
                $this->addFilm($tmdb_id);
            }

            Favorite::create([
                'user_id' => $authID,
                'tmdb_id' => $tmdb_id
            ]);

            $pointExist = Point::where([
                'user_id' => $authID,
                'type_id' => $tmdb_id,
                'type' => 'FAVORITE'
            ])->exists();

            if (!$pointExist) {
                $this->addPoint($authID, $tmdb_id, 10, 'FAVORITE');
            }

            return response()->json([
                'status' => 202,
                'message' => 'Request Created'
            ]);
        }
    }

    public function delete($tmdb_id)
    {
        Favorite::where([
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
