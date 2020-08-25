<?php

namespace App\Http\Controllers;

use App\Film;
use App\Following;
use App\Http\Traits\FilmTrait;
use App\Http\Traits\PointTrait;
use App\Review;
use App\ReviewLike;
use App\Watchlist;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;

class ReviewController extends Controller
{
    use FilmTrait;
    use PointTrait;

    public function showsFilmReviewFromAll($tmdbID)
    {
        Carbon::setLocale('id');

        $reviews = Review::with([
            'user'
        ])->where('tmdb_id', $tmdbID)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'status' => isset($reviews[0]) ? 101 : 606,
            'message' => isset($reviews[0]) ? 'Request Retrieved' : 'Request Not Found',
            'result' => isset($reviews[0]) ? $reviews : null
        ]);
    }

    public function showsFilmReviewFromFollowing($tmdbID)
    {
        Carbon::setLocale('id');

        $followings = Following::select('following_id')->where('user_id', Auth::id());

        $reviews = Review::with([
            'user'
        ])->where('tmdb_id', $tmdbID)
            ->whereIn('user_id', $followings)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'status' => isset($reviews[0]) ? 101 : 606,
            'message' => isset($reviews[0]) ? 'Request Retrieved' : 'Request Not Found',
            'result' => isset($reviews[0]) ? $reviews : null
        ]);
    }

    public function showsLikedReview($tmdbID)
    {
        Carbon::setLocale('id');

        $reviewLikes = ReviewLike::select('review_id')->where('user_id', Auth::id());

        $reviews = Review::with([
            'user'
        ])->where('tmdb_id', $tmdbID)
            ->whereIn('id', $reviewLikes)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'status' => isset($reviews[0]) ? 101 : 606,
            'message' => isset($reviews[0]) ? 'Request Retrieved' : 'Request Not Found',
            'result' => isset($reviews[0]) ? $reviews : null
        ]);
    }

    public function showsSelfReview($tmdbID)
    {
        Carbon::setLocale('id');

        $reviews = Review::with([
            'user'
        ])->where([
            'user_id' => Auth::id(),
            'tmdb_id' => $tmdbID
        ])->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'status' => isset($reviews[0]) ? 101 : 606,
            'message' => isset($reviews[0]) ? 'Request Retrieved' : 'Request Not Found',
            'result' => isset($reviews[0]) ? $reviews : null
        ]);
    }

    public function showsUserReview($userID)
    {
        Carbon::setLocale('id');

        $reviews = Review::with([
            'film'
        ])->where('user_id', $userID)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'status' => isset($reviews[0]) ? 101 : 606,
            'message' => isset($reviews[0]) ? 'Request Retrieved' : 'Request Not Found',
            'result' => isset($reviews[0]) ? $reviews : null
        ]);
    }

    public function showsTimeline()
    {
        Carbon::setLocale('id');

        $followings = Following::select('following_id')->where('user_id', Auth::id());

        $reviews = Review::with([
            'film', 'user'
        ])->whereIn('user_id', $followings)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'status' => isset($reviews[0]) ? 101 : 606,
            'message' => isset($reviews[0]) ? 'Request Retrieved' : 'Request Not Found',
            'result' => isset($reviews[0]) ? $reviews : null
        ]);
    }

    public function show($id)
    {
        Carbon::setLocale('id');

        $review = Review::with([
            'film', 'user'
        ])->findOrFail($id);

        return response()->json([
            'status' => 101,
            'message' => 'Request Retrieved',
            'result' => $review
        ]);
    }

    public function shows()
    {
        Carbon::setLocale('id');

        $reviews = Review::with([
            'film', 'user'
        ])->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'status' => isset($reviews[0]) ? 101 : 606,
            'message' => isset($reviews[0]) ? 'Request Retrieved' : 'Request Not Found',
            'result' => isset($reviews[0]) ? $reviews : null
        ]);
    }

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'rating' => 'required|numeric|min:0.5|max:5.0',
            'review_detail' => 'required',
            'watch_date' => 'required|date'
        ], [
            'rating.required' => 'Rating harus di isi',
            'review_detail.required' => 'Ulasan harus di isi',
            'watch_date.required' => 'Tanggal tonton harus di isi',
            'numeric' => 'Format rating tidak sesuai',
            'date' => 'Format tanggal tonton tidak sesuai',
            'min' => 'Rating minimal 0.5',
            'max' => 'Rating maksimal 5.0'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 626,
                'message' => 'Validator Fails',
                'result' => $validator->errors()->all()
            ]);
        } else {
            $authID = Auth::id();

            $tmdbID = $request['tmdb_id'];

            $filmExist = Film::where('tmdb_id', $tmdbID)->exists();

            if (!$filmExist) {
                $this->addFilm($tmdbID);
            }

            Review::create([
                'user_id' => $authID,
                'tmdb_id' => $tmdbID,
                'rating' => $request['rating'],
                'review_detail' => $request['review_detail'],
                'review_date' => Carbon::now('+07:00')->format('Y-m-d'),
                'watch_date' => $request['watch_date']
            ]);

            $watchlist = Watchlist::where([
                'user_id' => $authID,
                'tmdb_id' => $tmdbID
            ]);

            $inWatchlist = $watchlist->exists();

            if ($inWatchlist) {
                $watchlist->delete();
            }

            $this->addPoint($authID, $tmdbID, 30, 'REVIEW');

            return response()->json([
                'status' => 202,
                'message' => 'Request Created'
            ]);
        }
    }

    public function update(Request $request, $id)
    {
        $review = Review::findOrFail($id);

        if (Gate::allows('update-review', $review)) {
            $validator = Validator::make($request->all(), [
                'rating' => 'required|numeric|min:0.5|max:5.0',
                'review_detail' => 'required',
                'watch_date' => 'required|date'
            ], [
                'rating.required' => 'Rating harus di isi',
                'review_detail.required' => 'Ulasan harus di isi',
                'watch_date.required' => 'Tanggal tonton harus di isi',
                'numeric' => 'Format rating tidak sesuai',
                'date' => 'Format tanggal tonton tidak sesuai',
                'min' => 'Rating minimal 0.5',
                'max' => 'Rating maksimal 5.0'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 626,
                    'message' => 'Validator Fails',
                    'result' => $validator->errors()->all()
                ]);
            } else {
                $review->update([
                    'rating' => $request['rating'],
                    'review_detail' => $request['review_detail'],
                    'watch_date' => $request['watch_date']
                ]);

                return response()->json([
                    'status' => 303,
                    'message' => 'Request Updated'
                ]);
            }
        } else {
            return response()->json([
                'status' => 939,
                'message' => 'Unauthorized'
            ]);
        }
    }

    public function delete($id)
    {
        $review = Review::findOrFail($id);

        if (Gate::allows('delete-review', $review)) {
            $review->delete();

            return response()->json([
                'status' => 404,
                'message' => 'Request Deleted'
            ]);
        } else {
            return response()->json([
                'status' => 939,
                'message' => 'Unauthorized'
            ]);
        }
    }
}
