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
    public function show(Request $request, $tmdb_id) {
        $auth_uid = $request->header('auth_uid');

        $film = Film::with([
            'user'
        ])->where('tmdb_id', $tmdb_id)
          ->firstOrFail();
        
        $metadata = collect([
            'favorites' => Favorite::where('tmdb_id', $tmdb_id)->count(),
            'reviews' => Review::where('tmdb_id', $tmdb_id)->count(),
            'watchlists' => Watchlist::where('tmdb_id', $tmdb_id)->count(),
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

        $following = Following::select('following_id')->where('user_id', $auth_uid);
        
        $latest = Review::with([
            'user'
        ])->where('tmdb_id', $tmdb_id)
          ->whereIn('user_id', $following)
          ->orderBy('created_at', 'desc')
          ->take(5)
          ->get();
        
        $collection = collect([
            'film' => $film,
            'metadata' => $metadata,
            'latest' => isset($latest[0]) ? $latest : null
        ]);

        return response()->json([
            'status' => 101,
            'message' => 'Film Retrieved',
            'result' => $collection
        ]);
    }

    public function showSelf(Request $request, $tmdb_id) {
        $auth_uid = $request->header('auth_uid');
        $auth_token = $request->header('auth_token');

        $auth = User::where([
            'id' => $auth_uid,
            'token' => $auth_token
        ])->exists();
        
        if ($auth == true) {
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
                'last_rate' => $in_review == true ? $last_rate['rating'] : null,
                'in_favorite' => $in_favorite,
                'in_review' => $in_review,
                'in_watchlist' => $in_watchlist
            ]);

            return response()->json([
                'status' => 121,
                'message' => 'Self Film Retrieved',
                'result' => $collection
            ]);
        } else {
            return response()->json([
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
            $validator = Validator::make($request->all(), [
                'tmdb_id' => 'required|integer|unique:films',
                'title' => 'required|string|max:255',
                'release_date' => 'required|date',
                'poster' => 'required|string|max:255'
            ]);
    
            if ($validator->fails()) {
                return response()->json([
                    'status' => 999,
                    'message' => 'Validator Fails',
                    'result' => $validator->errors()->all()
                ]);
            } else {
                Film::create([
                    'tmdb_id' => $request['tmdb_id'],
                    'title' => $request['title'],
                    'release_date' => $request['release_date'],
                    'poster' => $request['poster']
                ]);
                
                return response()->json([
                    'status' => 102,
                    'message' => 'Film Added',
                ]);
            }
        } else {
            return response()->json([
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
            $validator = Validator::make($request->all(), [
                'overview' => 'required|string'
            ]);
    
            if ($validator->fails()) {
                return response()->json([
                    'status' => 999,
                    'message' => 'Validator Fails',
                    'result' => $validator->errors()->all()
                ]);
            } else {
                Film::where('tmdb_id', $tmdb_id)->firstOrFail()->update([
                    'overview' => $request['overview'],
                    'user_id' => $auth_uid
                ]);
                
                return response()->json([
                    'status' => 103,
                    'message' => 'Film Overview Updated'
                ]);
            }
        } else {
            return response()->json([
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
            Film::where('tmdb_id', $tmdb_id)->firstOrFail()->update([
                'overview' => null,
                'user_id' => null
            ]);
            
            return response()->json([
                'status' => 104,
                'message' => 'Film Overview Removed'
            ]);
        } else {
            return response()->json([
                'status' => 808,
                'message' => 'Invalid Credentials'
            ]);
        }
    }
}
