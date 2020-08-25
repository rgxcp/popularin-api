<?php

namespace App\Http\Controllers;

use App\Favorite;
use App\Film;
use App\Review;
use App\Watchlist;
use Illuminate\Support\Facades\Auth;

class FilmController extends Controller
{
    public function showSelf($tmdbID)
    {
        $authID = Auth::id();

        $lastRate = Review::select('rating')->where([
            'user_id' => $authID,
            'tmdb_id' => $tmdbID
        ])->latest()
            ->first();

        $inFavorite = Favorite::where([
            'user_id' => $authID,
            'tmdb_id' => $tmdbID
        ])->exists();

        $inReview = Review::where([
            'user_id' => $authID,
            'tmdb_id' => $tmdbID
        ])->exists();

        $inWatchlist = Watchlist::where([
            'user_id' => $authID,
            'tmdb_id' => $tmdbID
        ])->exists();

        $collection = collect([
            'last_rate' => isset($lastRate['rating']) ? $lastRate['rating'] : 0,
            'in_favorite' => $inFavorite,
            'in_review' => $inReview,
            'in_watchlist' => $inWatchlist
        ]);

        return response()->json([
            'status' => 101,
            'message' => 'Request Retrieved',
            'result' => $collection
        ]);
    }

    public function show($tmdbID)
    {
        $film = Film::where('tmdb_id', $tmdbID)->firstOrFail();

        $averageRating = Review::where('tmdb_id', $tmdbID)->avg('rating');

        $metadata = collect([
            'average_rating' => isset($averageRating) ? round($averageRating, 2) : 0,
            'total_favorite' => Favorite::where('tmdb_id', $tmdbID)->count(),
            'total_review' => Review::where('tmdb_id', $tmdbID)->count(),
            'total_watchlist' => Watchlist::where('tmdb_id', $tmdbID)->count()
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
