<?php

namespace App\Http\Controllers;

use App\Favorite;
use App\Film;
use App\Review;
use App\User;
use App\Watchlist;
use Illuminate\Http\Request;

class FilmController extends Controller
{
    public function show($tmdb_id) {
        $film = Film::with([
            'user'
        ])->where('tmdb_id', $tmdb_id)
          ->firstOrFail();

        return response()
            ->json([
                'status' => 101,
                'message' => 'Film Retrieved',
                'average_rating' => Review::where('tmdb_id', $tmdb_id)->avg('rating'),
                'total_favorites' => Favorite::where('tmdb_id', $tmdb_id)->count(),
                'total_reviews' => Review::where('tmdb_id', $tmdb_id)->count(),
                'total_watchlists' => Watchlist::where('tmdb_id', $tmdb_id)->count(),
                'rate_05' => Review::where('tmdb_id', $tmdb_id)->where('rating', 0.5)->count(),
                'rate_10' => Review::where('tmdb_id', $tmdb_id)->where('rating', 1)->count(),
                'rate_15' => Review::where('tmdb_id', $tmdb_id)->where('rating', 1.5)->count(),
                'rate_20' => Review::where('tmdb_id', $tmdb_id)->where('rating', 2)->count(),
                'rate_25' => Review::where('tmdb_id', $tmdb_id)->where('rating', 2.5)->count(),
                'rate_30' => Review::where('tmdb_id', $tmdb_id)->where('rating', 3)->count(),
                'rate_35' => Review::where('tmdb_id', $tmdb_id)->where('rating', 3.5)->count(),
                'rate_40.' => Review::where('tmdb_id', $tmdb_id)->where('rating', 4)->count(),
                'rate_45' => Review::where('tmdb_id', $tmdb_id)->where('rating', 4.5)->count(),
                'rate_50' => Review::where('tmdb_id', $tmdb_id)->where('rating', 5)->count(),
                'result' => $film
            ]);
    }
    
    public function shows() {
        $films = Film::with([
            'user'
        ])->orderBy('updated_at', 'desc')
          ->paginate(30);

        return response()
            ->json([
                'status' => 101,
                'message' => 'Films Retrieved',
                'results' => $films
            ]);
    }

    public function create(Request $request) {
        $auth = User::where([
            'id' => $request['user_id'],
            'token' => $request['user_token']
        ])->exists();
        
        if ($auth == true) {
            Film::create([
                'tmdb_id' => $request['tmdb_id'],
                'title' => $request['title'],
                'release_date' => $request['release_date'],
                'poster' => $request['poster']
            ]);
            
            return response()
                ->json([
                    'status' => 202,
                    'message' => 'Film Created',
                ]);
        } else {
            return response()
                ->json([
                    'status' => 505,
                    'message' => 'Not Authorized to Create Film'
                ]);
        }
    }

    public function update(Request $request, $tmdb_id) {
        $auth = User::where([
            'id' => $request['user_id'],
            'token' => $request['user_token']
        ])->exists();
        
        if ($auth == true) {
            Film::where('tmdb_id', $tmdb_id)
                ->firstOrFail()
                ->update([
                    'overview' => $request['overview'],
                    'user_id' => $request['user_id']
                ]);
            
            return response()->json([
                'status' => 303,
                'message' => 'Film Overview Updated'
            ]);
        } else {
            return response()
                ->json([
                    'status' => 505,
                    'message' => 'Not Authorized to Update Film Overview'
                ]);
        }
    }

    public function delete(Request $request, $tmdb_id) {
        $auth = User::where([
            'id' => $request['user_id'],
            'token' => $request['user_token']
        ])->exists();
        
        if ($auth == true) {
            Film::where('tmdb_id', $tmdb_id)
                ->findOrFail($tmdb_id)
                ->update([
                    'overview' => null,
                    'user_id' => null
                ]);
            
            return response()->json([
                'status' => 404,
                'message' => 'Film Overview Deleted'
            ]);
        } else {
            return response()
                ->json([
                    'status' => 505,
                    'message' => 'Not Authorized to Update Film'
                ]);
        }
    }
}
