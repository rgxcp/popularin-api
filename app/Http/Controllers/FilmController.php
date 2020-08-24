<?php

namespace App\Http\Controllers;

use Auth;
use App\Favorite;
use App\Film;
use App\Review;
use App\Watchlist;

class FilmController extends Controller
{
    public function showSelf($tmdb_id)
    {
        $authID = Auth::id();

        $last_rate = Review::select('rating')->where([
            'user_id' => $authID,
            'tmdb_id' => $tmdb_id
        ])->latest()
            ->first();

        $in_favorite = Favorite::where([
            'user_id' => $authID,
            'tmdb_id' => $tmdb_id
        ])->exists();

        $in_review = Review::where([
            'user_id' => $authID,
            'tmdb_id' => $tmdb_id
        ])->exists();

        $in_watchlist = Watchlist::where([
            'user_id' => $authID,
            'tmdb_id' => $tmdb_id
        ])->exists();

        $collection = collect([
            'last_rate' => isset($last_rate['rating']) ? $last_rate['rating'] : 0,
            'in_favorite' => $in_favorite,
            'in_review' => $in_review,
            'in_watchlist' => $in_watchlist
        ]);

        return response()->json([
            'status' => 101,
            'message' => 'Request Retrieved',
            'result' => $collection
        ]);
    }

    public function show($tmdb_id)
    {
        $film = Film::where('tmdb_id', $tmdb_id)->firstOrFail();

        $average_rating = Review::where('tmdb_id', $tmdb_id)->avg('rating');

        $metadata = collect([
            'average_rating' => isset($average_rating) ? round($average_rating, 2) : 0,
            'total_favorite' => Favorite::where('tmdb_id', $tmdb_id)->count(),
            'total_review' => Review::where('tmdb_id', $tmdb_id)->count(),
            'total_watchlist' => Watchlist::where('tmdb_id', $tmdb_id)->count()
        ]);

        $collection = collect([
            'film' => $film,
            'metadata' => $metadata
        ]);

        return response()->json([
            'status' => 101,
            'message' => 'Request Retrieved',
            'result' => $collection
        ]);
    }
}
