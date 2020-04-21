<?php

namespace App\Http\Controllers;

use App\Comment;
use App\Film;
use App\Following;
use App\Like;
use App\Review;
use App\User;
use App\Watchlist;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;

class ReviewController extends Controller
{
    public function showFilmReviewsFromAll($tmdb_id) {
        $reviews = Review::with([
            'user'
        ])->where('tmdb_id', $tmdb_id)
          ->orderBy('created_at', 'desc')
          ->paginate(30);
        
        return response()->json([
            'status' => isset($reviews[0]) ? 321 : 919,
            'message' => isset($reviews[0]) ? 'Film Reviews Retrieved' : 'Empty Film Reviews',
            'result' => isset($reviews[0]) ? $reviews : null
        ]);
    }

    public function showFilmReviewsFromFollowing(Request $request, $tmdb_id) {
        $auth_uid = $request->header('auth_uid');

        $following = Following::select('following_id')->where('user_id', $auth_uid);
        
        $reviews = Review::with([
            'user'
        ])->where('tmdb_id', $tmdb_id)
          ->whereIn('user_id', $following)
          ->orderBy('created_at', 'desc')
          ->paginate(30);

        return response()->json([
            'status' => isset($reviews[0]) ? 371 : 939,
            'message' => isset($reviews[0]) ? 'Following Reviews Retrieved' : 'Empty Following Reviews',
            'result' => isset($reviews[0]) ? $reviews : null
        ]);
    }

    public function showLikedReviews(Request $request, $tmdb_id) {
        $auth_uid = $request->header('auth_uid');

        $liked = Like::select('review_id')->where('user_id', $auth_uid);

        $reviews = Review::with([
            'user'
        ])->where('tmdb_id', $tmdb_id)
          ->whereIn('id', $liked)
          ->orderBy('created_at', 'desc')
          ->paginate(30);
        
        return response()->json([
            'status' => isset($reviews[0]) ? 321 : 919,
            'message' => isset($reviews[0]) ? 'Liked Reviews Retrieved' : 'Empty Liked Reviews',
            'result' => isset($reviews[0]) ? $reviews : null
        ]);
    }

    public function showSelfReviews(Request $request, $tmdb_id) {
        $auth_uid = $request->header('auth_uid');

        $reviews = Review::where([
            'user_id' => $auth_uid,
            'tmdb_id' => $tmdb_id
        ])->orderBy('created_at', 'desc')
          ->paginate(30);
        
        return response()->json([
            'status' => isset($reviews[0]) ? 331 : 919,
            'message' => isset($reviews[0]) ? 'User Reviews Retrieved' : 'Empty User Reviews',
            'result' => isset($reviews[0]) ? $reviews : null
        ]);
    }

    public function showUserReviews($user_id) {
        $reviews = Review::with([
            'film'
        ])->where('user_id', $user_id)
          ->orderBy('created_at', 'desc')
          ->paginate(30);

        return response()->json([
            'status' => isset($reviews[0]) ? 331 : 929,
            'message' => isset($reviews[0]) ? 'User Reviews Retrieved' : 'Empty User Reviews',
            'result' => isset($reviews[0]) ? $reviews : null
        ]);
    }

    public function show(Request $request, $id) {
        $auth_uid = $request->header('auth_uid');

        $review = Review::with([
            'film', 'user'
        ])->findOrFail($id);

        $metadata = collect([
            'comments' => Comment::where('review_id', $id)->count(),
            'likes' => Like::where('review_id', $id)->count(),
            'liked' => Like::where('user_id', $auth_uid)->where('review_id', $id)->exists(),
        ]);

        $collection = collect([
            'review' => $review,
            'metadata' => $metadata
        ]);

        return response()->json([
            'status' => 301,
            'message' => 'Review Retrieved',
            'result' => $collection
        ]);
    }

    public function shows() {
        $reviews = Review::with([
            'film', 'user'
        ])->orderBy('created_at', 'desc')
          ->paginate(30);

        return response()->json([
            'status' => 311,
            'message' => 'Reviews Retrieved',
            'result' => $reviews
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
            $validator = Validator::make($request->all(), [
                'tmdb_id' => 'required|integer',
                'rating' => 'required|numeric',
                'review_text' => 'required|string',
                'watch_date' => 'required|date'
            ]);
    
            if ($validator->fails()) {
                return response()->json([
                    'status' => 999,
                    'message' => 'Validator Fails',
                    'result' => $validator->errors()->all()
                ]);
            } else {
                $film_exist = Film::where('tmdb_id', $request['tmdb_id'])->exists();

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
                
                Review::create([
                    'user_id' => $auth_uid,
                    'tmdb_id' => $request['tmdb_id'],
                    'rating' => $request['rating'],
                    'review_text' => $request['review_text'],
                    'review_date' => Carbon::now()->format('Y-m-d'),
                    'watch_date' => $request['watch_date']
                ]);
                
                return response()->json([
                    'status' => 302,
                    'message' => 'Review Added',
                    'film_exist' => $film_exist
                ]);
            }
        } else {
            return response()->json([
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
            $validator = Validator::make($request->all(), [
                'rating' => 'required|numeric',
                'review_text' => 'required|string',
                'watch_date' => 'required|date'
            ]);
    
            if ($validator->fails()) {
                return response()->json([
                    'status' => 999,
                    'message' => 'Validator Fails',
                    'result' => $validator->errors()->all()
                ]);
            } else {
                Review::findOrFail($id)->update([
                    'rating' => $request['rating'],
                    'review_text' => $request['review_text'],
                    'watch_date' => $request['watch_date']
                ]);

                return response()->json([
                    'status' => 303,
                    'message' => 'Review Updated'
                ]);
            }
        } else {
            return response()->json([
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
            Review::findOrFail($id)->delete();

            return response()->json([
                'status' => 304,
                'message' => 'Review Deleted'
            ]);
        } else {
            return response()->json([
                'status' => 808,
                'message' => 'Invalid Credentials'
            ]);
        }
    }
}
