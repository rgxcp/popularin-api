<?php

namespace App\Http\Controllers;

use App\Review;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class ReviewController extends Controller
{
    public function show($id) {
        $review = Review::with([
            'film', 'user'
        ])->findOrFail($id);

        return response()
            ->json([
                'status' => 201,
                'message' => 'Success',
                'result' => $review
            ]);
    }

    public function showFilmReviews($tmdb_id) {
        $reviews = Review::with([
            'user'
        ])->where('tmdb_id', $tmdb_id)->get();

        return response()
            ->json([
                'status' => 201,
                'message' => 'Success',
                'results' => $reviews
            ]);
    }

    public function showUserReviews($user_id) {
        $reviews = Review::with([
            'film'
        ])->where('user_id', $user_id)->get();

        return response()
            ->json([
                'status' => 201,
                'message' => 'Success',
                'results' => $reviews
            ]);
    }

    public function shows() {
        $reviews = Review::with([
            'film', 'user'
        ])->orderBy('created_at', 'desc')->get();

        return response()
            ->json([
                'status' => 201,
                'message' => 'Success',
                'results' => $reviews
            ]);
    }

    public function create(Request $request) {
        $match = User::select('token')
            ->where([
                'id' => $request['user_id'],
                'token' => $request['user_token']
            ])->exists();
        
        if ($match == true) {
            Review::create([
                'user_id' => $request['user_id'],
                'tmdb_id' => $request['tmdb_id'],
                'rating' => $request['rating'],
                'review_text' => $request['review_text'],
                'review_date' => Carbon::now()->format('Y-m-d'),
                'watch_date' => $request['watch_date']
            ]);

            $is_film_exist = Film::select('tmdb_id')
                ->where([
                    'tmdb_id' => $request['tmdb_id']
                ])->exists();

            return response()
                ->json([
                    'status' => 201,
                    'is_film_exist' => $is_film_exist,
                    'message' => 'Created'
                ]);
        } else {
            return response()
                ->json([
                    'status' => 403,
                    'message' => 'Invalid Token'
                ]);
        }
    }

    public function update(Request $request, $id) {
        $match = User::select('token')
            ->where([
                'id' => $request['user_id'],
                'token' => $request['user_token']
            ])->exists();
        
        if ($match == true) {
            Review::findOrFail($id)
                ->update([
                    'rating' => $request['rating'],
                    'review_text' => $request['review_text'],
                    'watch_date' => $request['watch_date']
                ]);
            
            return response()
                ->json([
                    'status' => 201,
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

    public function delete(Request $request, $id) {
        $match = User::select('token')
            ->where([
                'id' => $request['user_id'],
                'token' => $request['user_token']
            ])->exists();
        
        if ($match == true) {
            Review::findOrFail($id)
                ->delete();

            return response()
                ->json([
                    'status' => 201,
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
