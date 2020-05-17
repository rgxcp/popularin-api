<?php

namespace App\Http\Controllers;

use App\Favorite;
use App\Film;
use App\Following;
use App\Review;
use App\User;
use App\Watchlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FilmController extends Controller
{
    public function self(Request $request, $tmdb_id) {
        $auth_uid = $request->header('auth_uid');
        $auth_token = $request->header('auth_token');

        $isAuth = User::where([
            'id' => $auth_uid,
            'token' => $auth_token
        ])->exists();
        
        if ($isAuth) {
            $last_rate = Review::select('rating')->where([
                'user_id' => $auth_uid,
                'tmdb_id' => $tmdb_id
            ])->latest()
              ->first();
            
            $in_favorite = Favorite::where([
                'user_id' => $auth_uid,
                'tmdb_id' => $tmdb_id
            ])->exists();

            $in_review = Review::where([
                'user_id' => $auth_uid,
                'tmdb_id' => $tmdb_id
            ])->exists();
    
            $in_watchlist = Watchlist::where([
                'user_id' => $auth_uid,
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
        } else {
            return response()->json([
                'status' => 616,
                'message' => 'Invalid Credentials'
            ]);
        }
    }

    public function show($tmdb_id) {
        $film = Film::where('tmdb_id', $tmdb_id)->firstOrFail();

        $average_rating = Review::where('tmdb_id', $tmdb_id)->avg('rating');
        
        $metadata = collect([
            'average_rating' => isset($average_rating) ? $average_rating : 0,
            'total_favorite' => Favorite::where('tmdb_id', $tmdb_id)->count(),
            'total_review' => Review::where('tmdb_id', $tmdb_id)->count(),
            'total_watchlist' => Watchlist::where('tmdb_id', $tmdb_id)->count()
            /*
            'total_rate_05' => Review::where(['tmdb_id' => $tmdb_id, 'rating' => 0.5])->count(),
            'total_rate_10' => Review::where(['tmdb_id' => $tmdb_id, 'rating' => 1.0])->count(),
            'total_rate_15' => Review::where(['tmdb_id' => $tmdb_id, 'rating' => 1.5])->count(),
            'total_rate_20' => Review::where(['tmdb_id' => $tmdb_id, 'rating' => 2.0])->count(),
            'total_rate_25' => Review::where(['tmdb_id' => $tmdb_id, 'rating' => 2.5])->count(),
            'total_rate_30' => Review::where(['tmdb_id' => $tmdb_id, 'rating' => 3.0])->count(),
            'total_rate_35' => Review::where(['tmdb_id' => $tmdb_id, 'rating' => 3.5])->count(),
            'total_rate_40' => Review::where(['tmdb_id' => $tmdb_id, 'rating' => 4.0])->count(),
            'total_rate_45' => Review::where(['tmdb_id' => $tmdb_id, 'rating' => 4.5])->count(),
            'total_rate_50' => Review::where(['tmdb_id' => $tmdb_id, 'rating' => 5.0])->count()
            */
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
