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
    public function show($id) {
        $review = Review::with([
            'film', 'user'
        ])->findOrFail($id);

        return response()
            ->json([
                'status' => 101,
                'message' => 'Review Retrieved',
                'total_comments' => Comment::where('review_id', $id)->count(),
                'result' => $review
            ]);
    }

    public function showFilmReviews($tmdb_id) {
        $reviews = Review::with([
            'user'
        ])->where('tmdb_id', $tmdb_id)
          ->paginate(30);

        return response()
            ->json([
                'status' => 101,
                'message' => 'Reviews Retrieved',
                'total_reviews' => Review::where('tmdb_id', $tmdb_id)->count(),
                'results' => $reviews
            ]);
    }

    public function showUserReviews($user_id) {
        $reviews = Review::with([
            'film'
        ])->where('user_id', $user_id)
          ->paginate(30);

        return response()
            ->json([
                'status' => 101,
                'message' => 'Reviews Retrieved',
                'total_reviews' => Review::where('user_id', $user_id)->count(),
                'results' => $reviews
            ]);
    }

    public function shows() {
        $reviews = Review::with([
            'film', 'user'
        ])->orderBy('created_at', 'desc')
          ->paginate(30);

        return response()
            ->json([
                'status' => 101,
                'message' => 'Reviews Retrieved',
                'results' => $reviews
            ]);
    }

    public function create(Request $request) {
        $auth = User::where([
            'id' => $request['user_id'],
            'token' => $request['user_token']
        ])->exists();
        
        if ($auth == true) {
            Review::create([
                'user_id' => $request['user_id'],
                'tmdb_id' => $request['tmdb_id'],
                'rating' => $request['rating'],
                'review_text' => $request['review_text'],
                'review_date' => Carbon::now()->format('Y-m-d'),
                'watch_date' => $request['watch_date']
            ]);

            $film_exist = Film::select('tmdb_id')
                ->where([
                    'tmdb_id' => $request['tmdb_id']
                ])->exists();
            
            $in_watchlist = Watchlist::where([
                'user_id' => $request['user_id'],
                'tmdb_id' => $request['tmdb_id']
            ])->exists();
    
            if ($in_watchlist == true) {
                $watchlist_id = Watchlist::select('id')
                    ->where([
                        'user_id' => $request['user_id'],
                        'tmdb_id' => $request['tmdb_id']
                    ])->firstOrFail();
                
                Watchlist::findOrFail($watchlist_id['id'])
                    ->delete();
            } 

            return response()
                ->json([
                    'status' => 202,
                    'message' => 'Review Added',
                    'film_exist' => $film_exist
                ]);
        } else {
            return response()
                ->json([
                    'status' => 505,
                    'message' => 'Not Authorized to Add Review'
                ]);
        }
    }

    public function update(Request $request, $id) {
        $user_id = Review::select('user_id')
            ->where([
                'id' => $id
            ])->firstOrFail();
        
        $auth = User::where([
            'id' => $user_id['user_id'],
            'token' => $request['user_token']
        ])->exists();
        
        if ($auth == true) {
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
                    'status' => 505,
                    'message' => 'Not Authorized to Update Review'
                ]);
        }
    }

    public function delete(Request $request, $id) {
        $user_id = Review::select('user_id')
            ->where([
                'id' => $id
            ])->firstOrFail();
        
        $auth = User::where([
            'id' => $user_id['user_id'],
            'token' => $request['user_token']
        ])->exists();
        
        if ($auth == true) {
            Review::findOrFail($id)
                ->delete();

            return response()
                ->json([
                    'status' => 404,
                    'message' => 'Review Deleted'
                ]);
        } else {
            return response()
                ->json([
                    'status' => 505,
                    'message' => 'Not Authorized to Delete Review'
                ]);
        }
    }
}
