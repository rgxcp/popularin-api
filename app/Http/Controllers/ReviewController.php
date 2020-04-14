<?php

namespace App\Http\Controllers;

use App\Comment;
use App\Film;
use App\Review;
use App\User;
use App\Watchlist;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class ReviewController extends Controller
{
    public function showFilmReviews($tmdb_id) {
        $reviews = Review::with([
            'user'
        ])->where('tmdb_id', $tmdb_id)
          ->orderBy('created_at', 'desc')
          ->paginate(30);
        
        return response()
            ->json([
                'status' => 321,
                'message' => 'Film Reviews Retrieved',
                'results' => $reviews
            ]);
    }

    public function showUserReviews($user_id) {
        $reviews = Review::with([
            'film'
        ])->where('user_id', $user_id)
          ->orderBy('created_at', 'desc')
          ->paginate(30);

        return response()
            ->json([
                'status' => 331,
                'message' => 'User Reviews Retrieved',
                'results' => $reviews
            ]);
    }

    public function show($id) {
        $review = Review::with([
            'film', 'user'
        ])->findOrFail($id);

        return response()
            ->json([
                'status' => 301,
                'message' => 'Review Retrieved',
                'total_comments' => Comment::where('review_id', $id)->count(),
                'result' => $review
            ]);
    }

    public function shows() {
        $reviews = Review::with([
            'film', 'user'
        ])->orderBy('created_at', 'desc')
          ->paginate(30);

        return response()
            ->json([
                'status' => 311,
                'message' => 'Reviews Retrieved',
                'results' => $reviews
            ]);
    }

    public function create(Request $request) {
        $auth_uid = $request->header('auth_uid');
        $auth_token = $request->header('auth_token');
        
        $auth = User::where([
            'id' => $auth_uid,
            'token' => $auth_token
        ])->exists();
        
        if ($auth == true) {
            $in_watchlist = Watchlist::where([
                'user_id' => $auth_uid,
                'tmdb_id' => $request['tmdb_id']
            ])->exists();
    
            if ($in_watchlist == true) {
                Watchlist::where([
                    'user_id' => $auth_uid,
                    'tmdb_id' => $request['tmdb_id']
                ])->delete();
            }

            $this->validate($request, [
                'tmdb_id' => 'required|integer',
                'rating' => 'required|numeric',
                'review_text' => 'required|string',
                'watch_date' => 'required|date'
            ],[
                'required' => 'Input field harus di isi.',
                'integer' => 'Format input field harus berupa integer.',
                'numeric' => 'Format input field harus berupa double',
                'string' => 'Format input field harus berupa string.',
                'date' => 'Format input field harus berupa date.'
            ]);
            
            Review::create([
                'user_id' => $auth_uid,
                'tmdb_id' => $request['tmdb_id'],
                'rating' => $request['rating'],
                'review_text' => $request['review_text'],
                'review_date' => Carbon::now()->format('Y-m-d'),
                'watch_date' => $request['watch_date']
            ]);

            $film_exist = Film::where([
                'tmdb_id' => $request['tmdb_id']
            ])->exists();
            
            return response()
                ->json([
                    'status' => 302,
                    'message' => 'Review Added',
                    'film_exist' => $film_exist
                ]);
        } else {
            return response()
                ->json([
                    'status' => 808,
                    'message' => 'Invalid Credentials'
                ]);
        }
    }

    public function update(Request $request, $id) {
        $auth_uid = Review::select('user_id')->where('id', $id)->firstOrFail();
        $auth_token = $request->header('auth_token');
        
        $auth = User::where([
            'id' => $auth_uid['user_id'],
            'token' => $auth_token
        ])->exists();
        
        if ($auth == true) {
            $this->validate($request, [
                'rating' => 'required|numeric',
                'review_text' => 'required|string',
                'watch_date' => 'required|date'
            ],[
                'required' => 'Input field harus di isi.',
                'numeric' => 'Format input field harus berupa double',
                'string' => 'Format input field harus berupa string.',
                'date' => 'Format input field harus berupa date.'
            ]);

            Review::findOrFail($id)
                ->update([
                    'rating' => $request['rating'],
                    'review_text' => $request['review_text'],
                    'watch_date' => $request['watch_date']
                ]);
            
            return response()
                ->json([
                    'status' => 303,
                    'message' => 'Review Updated'
                ]);
        } else {
            return response()
                ->json([
                    'status' => 808,
                    'message' => 'Invalid Credentials'
                ]);
        }
    }

    public function delete(Request $request, $id) {
        $auth_uid = Review::select('user_id')->where('id', $id)->firstOrFail();
        $auth_token = $request->header('auth_token');
        
        $auth = User::where([
            'id' => $auth_uid['user_id'],
            'token' => $auth_token
        ])->exists();
        
        if ($auth == true) {
            Review::findOrFail($id)
                ->delete();

            return response()
                ->json([
                    'status' => 304,
                    'message' => 'Review Deleted'
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
