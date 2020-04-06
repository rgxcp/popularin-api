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
            'contributor_info'
        ])->where('tmdb_id', $tmdb_id)->firstOrFail();

        return response()->json([
            'status' => 200,
            'message' => 'Success',
            'average_rating' => Review::where('tmdb_id', $tmdb_id)->avg('rating'),
            'total_favorites' => Favorite::where('tmdb_id', $tmdb_id)->count(),
            'total_reviews' => Review::where('tmdb_id', $tmdb_id)->count(),
            'total_watchlists' => Watchlist::where('tmdb_id', $tmdb_id)->count(),
            'result' => $film
        ]);
    }
    
    public function shows() {
        $films = Film::with([
            'contributor_info', 'total_reviews'
        ])->get();

        return response()
            ->json([
                'status' => 200,
                'message' => 'Success',
                'results' => $films
            ]);
    }

    public function create(Request $request) {
        $match = User::select('token')
            ->where([
                'id' => $request['user_id'],
                'token' => $request['user_token']
            ])->exists();
        
        if ($match == true) {
            $film = Film::create($request->all());
            
            return response()
                ->json([
                    'status' => 201,
                    'message' => 'Created',
                    'result' => $film
                ]);
        } else {
            return response()
                ->json([
                    'status' => 403,
                    'message' => 'Invalid Token'
                ]);
        }
    }

    public function update(Request $request, $tmdb_id) {
        $match = User::select('token')
            ->where([
                'id' => $request['user_id'],
                'token' => $request['user_token']
            ])->exists();
        
        if ($match == true) {
            Film::where('tmdb_id', $tmdb_id)
                ->firstOrFail()
                ->update([
                    'overview' => $request['overview'],
                    'contributor' => $request['contributor']
                ]);
            
            return response()->json([
                'status' => 200,
                'message' => 'Updated'
            ]);
        } else {
            return response()
                ->json([
                    'status' => 403,
                    'message' => 'Invalid Token'
                ]);
        }
    }

    public function delete(Request $request, $tmdb_id) {
        $match = User::select('token')
            ->where([
                'id' => $request['user_id'],
                'token' => $request['user_token']
            ])->exists();
        
        if ($match == true) {
            Film::where('tmdb_id', $tmdb_id)
                ->findOrFail($tmdb_id)
                ->update([
                    'overview' => null,
                    'contributor' => null
                ]);
            
            return response()->json([
                'status' => 200,
                'message' => 'Deleted'
            ]);
        } else {
            return response()
                ->json([
                    'status' => 403,
                    'message' => 'Invalid Token'
                ]);
        }
    }
}
