<?php

namespace App\Http\Controllers;

use App\Following;
use App\Review;
use App\ReviewLike;
use Illuminate\Support\Facades\Auth;

class ReviewLikeController extends Controller
{
    public function showsReviewLikeFromAll($reviewID)
    {
        $reviewLikes = ReviewLike::with([
            'user'
        ])->where('review_id', $reviewID)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'status' => isset($reviewLikes[0]) ? 101 : 606,
            'message' => isset($reviewLikes[0]) ? 'Request Retrieved' : 'Request Not Found',
            'result' => isset($reviewLikes[0]) ? $reviewLikes : null
        ]);
    }

    public function showsReviewLikeFromFollowing($reviewID)
    {
        $followings = Following::select('following_id')->where('user_id', Auth::id());

        $reviewLikes = ReviewLike::with([
            'user'
        ])->where('review_id', $reviewID)
            ->whereIn('user_id', $followings)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'status' => isset($reviewLikes[0]) ? 101 : 606,
            'message' => isset($reviewLikes[0]) ? 'Request Retrieved' : 'Request Not Found',
            'result' => isset($reviewLikes[0]) ? $reviewLikes : null
        ]);
    }

    public function create($reviewID)
    {
        Review::findOrFail($reviewID);

        $authID = Auth::id();

        $isLiked = ReviewLike::where([
            'user_id' => $authID,
            'review_id' => $reviewID
        ])->exists();

        if ($isLiked) {
            return response()->json([
                'status' => 666,
                'message' => 'Already Liked'
            ]);
        } else {
            ReviewLike::create([
                'user_id' => $authID,
                'review_id' => $reviewID
            ]);

            return response()->json([
                'status' => 202,
                'message' => 'Request Created'
            ]);
        }
    }

    public function delete($reviewID)
    {
        ReviewLike::where([
            'user_id' => Auth::id(),
            'review_id' => $reviewID
        ])->firstOrFail()
            ->delete();

        return response()->json([
            'status' => 404,
            'message' => 'Request Deleted'
        ]);
    }
}
