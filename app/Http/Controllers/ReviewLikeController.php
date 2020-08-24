<?php

namespace App\Http\Controllers;

use Auth;
use App\Following;
use App\Review;
use App\ReviewLike;

class ReviewLikeController extends Controller
{
    public function showsReviewLikeFromAll($review_id)
    {
        $reviewLikes = ReviewLike::with([
            'user'
        ])->where('review_id', $review_id)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'status' => isset($reviewLikes[0]) ? 101 : 606,
            'message' => isset($reviewLikes[0]) ? 'Request Retrieved' : 'Request Not Found',
            'result' => isset($reviewLikes[0]) ? $reviewLikes : null
        ]);
    }

    public function showsReviewLikeFromFollowing($review_id)
    {
        $followings = Following::select('following_id')->where('user_id', Auth::id());

        $reviewLikes = ReviewLike::with([
            'user'
        ])->where('review_id', $review_id)
            ->whereIn('user_id', $followings)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'status' => isset($reviewLikes[0]) ? 101 : 606,
            'message' => isset($reviewLikes[0]) ? 'Request Retrieved' : 'Request Not Found',
            'result' => isset($reviewLikes[0]) ? $reviewLikes : null
        ]);
    }

    public function create($review_id)
    {
        Review::findOrFail($review_id);

        $authID = Auth::id();

        $isLiked = ReviewLike::where([
            'user_id' => $authID,
            'review_id' => $review_id
        ])->exists();

        if ($isLiked) {
            return response()->json([
                'status' => 666,
                'message' => 'Already Liked'
            ]);
        } else {
            ReviewLike::create([
                'user_id' => $authID,
                'review_id' => $review_id
            ]);

            return response()->json([
                'status' => 202,
                'message' => 'Request Created'
            ]);
        }
    }

    public function delete($review_id)
    {
        ReviewLike::where([
            'user_id' => Auth::id(),
            'review_id' => $review_id
        ])->firstOrFail()
            ->delete();

        return response()->json([
            'status' => 404,
            'message' => 'Request Deleted'
        ]);
    }
}
