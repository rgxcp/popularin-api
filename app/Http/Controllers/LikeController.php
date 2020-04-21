<?php

namespace App\Http\Controllers;

use App\Following;
use App\Like;
use App\Review;
use App\User;
use Illuminate\Http\Request;

class LikeController extends Controller
{
    public function showLikesFromAll(Request $request, $review_id) {
        $likes = Like::with([
            'user'
        ])->where('review_id', $review_id)
          ->orderBy('created_at', 'desc')
          ->paginate(30);
        
        return response()->json([
            'status' => isset($likes[0]) ? 501 : 959,
            'message' => isset($likes[0]) ? 'Likes Retrieved' : 'Empty Likes',
            'result' => isset($likes[0]) ? $likes : null
        ]);
    }

    public function showLikesFromFollowing(Request $request, $review_id) {
        $auth_uid = $request->header('auth_uid');

        $following = Following::select('following_id')->where('user_id', $auth_uid);

        $likes = Like::with([
            'user'
        ])->where('review_id', $review_id)
          ->whereIn('user_id', $following)
          ->orderBy('created_at', 'desc')
          ->paginate(30);
        
        return response()->json([
            'status' => isset($likes[0]) ? 501 : 959,
            'message' => isset($likes[0]) ? 'Likes Retrieved' : 'Empty Likes',
            'result' => isset($likes[0]) ? $likes : null
        ]);
    }

    public function create(Request $request, $review_id) {
        $auth_uid = $request->header('auth_uid');
        $auth_token = $request->header('auth_token');
        
        $auth = User::where([
            'id' => $auth_uid,
            'token' => $auth_token
        ])->exists();
        
        if ($auth == true) {
            $already_liked = Like::where([
                'user_id' => $auth_uid,
                'review_id' => $review_id
            ])->exists();

            if ($already_liked == true) {
                return response()->json([
                    'status' => 925,
                    'message' => 'Review Already Liked'
                ]);
            } else {
                Like::create([
                    'user_id' => $auth_uid,
                    'review_id' => $review_id
                ]);
                
                return response()->json([
                    'status' => 502,
                    'message' => 'Review Liked'
                ]);
            }
        } else {
            return response()->json([
                'status' => 808,
                'message' => 'Invalid Credentials'
            ]);
        }
    }

    public function delete(Request $request, $review_id) {
        $auth_uid = Like::select('user_id')->where('review_id', $review_id)->firstOrFail();
        $auth_token = $request->header('auth_token');
        
        $auth = User::where([
            'id' => $auth_uid['user_id'],
            'token' => $auth_token
        ])->exists();
        
        if ($auth == true) {
            Like::where('review_id', $review_id)->firstOrFail()->delete();
            
            return response()->json([
                'status' => 504,
                'message' => 'Review Unliked'
            ]);
        } else {
            return response()->json([
                'status' => 808,
                'message' => 'Invalid Credentials'
            ]);
        }
    }
}
