<?php

namespace App\Http\Controllers;

use App\Favorite;
use App\Film;
use App\Following;
use App\Review;
use App\User;
use App\Watchlist;
use Illuminate\Http\Request;

class FilmController extends Controller
{
    public function show(Request $request, $tmdb_id) {
        $auth_uid = $request->header('auth_uid');

        $film = Film::with([
            'user'
        ])->where('tmdb_id', $tmdb_id)
          ->firstOrFail();
        
        $metadata = collect([
            'total_favorites' => Favorite::where('tmdb_id', $tmdb_id)->count(),
            'total_reviews' => Review::where('tmdb_id', $tmdb_id)->count(),
            'total_watchlists' => Watchlist::where('tmdb_id', $tmdb_id)->count(),
            'average_rating' => Review::where('tmdb_id', $tmdb_id)->avg('rating'),
            'rate_0.5' => Review::where('tmdb_id', $tmdb_id)->where('rating', 0.5)->count(),
            'rate_1.0' => Review::where('tmdb_id', $tmdb_id)->where('rating', 1)->count(),
            'rate_1.5' => Review::where('tmdb_id', $tmdb_id)->where('rating', 1.5)->count(),
            'rate_2.0' => Review::where('tmdb_id', $tmdb_id)->where('rating', 2)->count(),
            'rate_2.5' => Review::where('tmdb_id', $tmdb_id)->where('rating', 2.5)->count(),
            'rate_3.0' => Review::where('tmdb_id', $tmdb_id)->where('rating', 3)->count(),
            'rate_3.5' => Review::where('tmdb_id', $tmdb_id)->where('rating', 3.5)->count(),
            'rate_4.0.' => Review::where('tmdb_id', $tmdb_id)->where('rating', 4)->count(),
            'rate_4.5' => Review::where('tmdb_id', $tmdb_id)->where('rating', 4.5)->count(),
            'rate_5.0' => Review::where('tmdb_id', $tmdb_id)->where('rating', 5)->count(),
        ]);

        $following = Following::select('following_id')
            ->where('user_id', $auth_uid);

        $activity = Review::with([
            'user'
        ])->where('tmdb_id', $tmdb_id)
          ->whereIn('user_id', $following)
          ->orderBy('created_at', 'desc')
          ->take(5)
          ->get();
        
        $result = collect([
            'film' => $film,
            'metadata' => $metadata,
            'activity' => isset($activity[0]) ? $activity : null
        ]);

        return response()
            ->json([
                'status' => 101,
                'message' => 'Film Retrieved',
                'result' => $result
            ]);
    }
    
    public function self(Request $request, $tmdb_id) {
        $auth_uid = $request->header('auth_uid');
        $auth_token = $request->header('auth_token');

        $auth = User::where([
            'id' => $auth_uid,
            'token' => $auth_token
        ])->exists();
        
        if ($auth == true) {
            $last_rate = Review::select('rating')
                ->where([
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

            return response()
                ->json([
                    'status' => 121,
                    'message' => 'Self Film Retrieved',
                    'last_rate' => $in_review == false ? 0 : $last_rate['rating'],
                    'in_favorite' => $in_favorite,
                    'in_review' => $in_review,
                    'in_watchlist' => $in_watchlist
                ]);
        } else {
            return response()
                ->json([
                    'status' => 808,
                    'message' => 'Invalid Credentials'
                ]);
        }
    }

    public function create(Request $request) {
        $auth_uid = $request->header('auth_uid');
        $auth_token = $request->header('auth_token');
        
        $auth = User::where([
            'id' => $auth_uid,
            'token' => $auth_token
        ])->exists();
        
        if ($auth == true) {
            $this->validate($request, [
                'tmdb_id' => 'required|integer|unique:films',
                'title' => 'required|string|max:255',
                'release_date' => 'required|date',
                'poster' => 'required|string|max:255'
            ],[
                'required' => 'Input field harus di isi.',
                'integer' => 'Format input field harus berupa integer.',
                'string' => 'Format input field harus berupa string.',
                'date' => 'Format input field harus berupa date.',
                'max' => 'Input yang dimasukkan melebihi batas.',
                'unique' => 'Film tersebut sudah ada.'
            ]);

            Film::create([
                'tmdb_id' => $request['tmdb_id'],
                'title' => $request['title'],
                'release_date' => $request['release_date'],
                'poster' => $request['poster']
            ]);
            
            return response()
                ->json([
                    'status' => 102,
                    'message' => 'Film Added',
                ]);
        } else {
            return response()
                ->json([
                    'status' => 808,
                    'message' => 'Invalid Credentials'
                ]);
        }
    }

    public function update(Request $request, $tmdb_id) {
        $auth_uid = $request->header('auth_uid');
        $auth_token = $request->header('auth_token');
        
        $auth = User::where([
            'id' => $auth_uid,
            'token' => $auth_token
        ])->exists();
        
        if ($auth == true) {
            $this->validate($request, [
                'overview' => 'required|string',
            ],[
                'required' => 'Input field harus di isi.',
                'string' => 'Format input field harus berupa string.'
            ]);

            Film::where('tmdb_id', $tmdb_id)
                ->firstOrFail()
                ->update([
                    'overview' => $request['overview'],
                    'user_id' => $auth_uid
                ]);
            
            return response()->json([
                'status' => 103,
                'message' => 'Film Overview Updated'
            ]);
        } else {
            return response()
                ->json([
                    'status' => 808,
                    'message' => 'Invalid Credentials'
                ]);
        }
    }

    public function delete(Request $request, $tmdb_id) {
        $auth_uid = $request->header('auth_uid');
        $auth_token = $request->header('auth_token');
        
        $auth = User::where([
            'id' => $auth_uid,
            'token' => $auth_token
        ])->exists();
        
        if ($auth == true) {
            Film::where('tmdb_id', $tmdb_id)
                ->firstOrFail()
                ->update([
                    'overview' => null,
                    'user_id' => null
                ]);
            
            return response()
                ->json([
                    'status' => 104,
                    'message' => 'Film Overview Removed'
                ]);
        } else {
            return response()
                ->json([
                    'status' => 808,
                    'message' => 'Invalid Credentials'
                ]);
        }
    }
}
