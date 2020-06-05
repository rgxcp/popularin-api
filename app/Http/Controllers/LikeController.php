<?php

namespace App\Http\Controllers;

use Auth;
use App\Following;
use App\Like;

class LikeController extends Controller
{
    public function showLikesFromAll($review_id) {
        $likes = Like::with([
            'user'
        ])->where('review_id', $review_id)
          ->orderBy('created_at', 'desc')
          ->paginate(50);
        
        return response()->json([
            'status' => isset($likes[0]) ? 101 : 606,
            'message' => isset($likes[0]) ? 'Request Retrieved' : 'Request Not Found',
            'result' => isset($likes[0]) ? $likes : null
        ]);
    }

    public function showLikesFromFollowing($review_id) {
        $authID = Auth::user()->id;

        $followings = Following::select('following_id')->where('user_id', $authID);

        $likes = Like::with([
            'user'
        ])->where('review_id', $review_id)
          ->whereIn('user_id', $followings)
          ->orderBy('created_at', 'desc')
          ->paginate(50);
        
        return response()->json([
            'status' => isset($likes[0]) ? 101 : 606,
            'message' => isset($likes[0]) ? 'Request Retrieved' : 'Request Not Found',
            'result' => isset($likes[0]) ? $likes : null
        ]);
    }

    public function create($review_id) {
        $authID = Auth::user()->id;

        $isLiked = Like::where([
            'user_id' => $authID,
            'review_id' => $review_id
        ])->exists();

        if ($isLiked) {
            return response()->json([
                'status' => 666,
                'message' => 'Already Liked'
            ]);
        } else {
            Like::create([
                'user_id' => $authID,
                'review_id' => $review_id
            ]);
            
            return response()->json([
                'status' => 202,
                'message' => 'Request Created'
            ]);
        }
    }

    public function delete($review_id) {
        $authID = Auth::user()->id;
        
        Like::where([
            'user_id' => $authID,
            'review_id' => $review_id
        ])->firstOrFail()
          ->delete();
        
        return response()->json([
            'status' => 404,
            'message' => 'Request Deleted'
        ]);
    }
}
