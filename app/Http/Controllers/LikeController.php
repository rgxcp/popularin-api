<?php

namespace App\Http\Controllers;

use App\Following;
use App\Like;
use App\Review;
use App\User;
use Illuminate\Http\Request;

class LikeController extends Controller
{
    public function showLikesFromAll($review_id) {
        $likes = Like::with([
            'user'
        ])->where('review_id', $review_id)
          ->orderBy('created_at', 'desc')
          ->paginate(30);
        
        return response()->json([
            'status' => isset($likes[0]) ? 101 : 606,
            'message' => isset($likes[0]) ? 'Request Retrieved' : 'Request Not Found',
            'result' => isset($likes[0]) ? $likes : null
        ]);
    }

    public function showLikesFromFollowing(Request $request, $review_id) {
        $auth_uid = $request->header('auth_uid');

        $followings = Following::select('following_id')->where('user_id', $auth_uid);

        $likes = Like::with([
            'user'
        ])->where('review_id', $review_id)
          ->whereIn('user_id', $followings)
          ->orderBy('created_at', 'desc')
          ->paginate(30);
        
        return response()->json([
            'status' => isset($likes[0]) ? 101 : 606,
            'message' => isset($likes[0]) ? 'Request Retrieved' : 'Request Not Found',
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
        
        if ($auth) {
            $already_liked = Like::where([
                'user_id' => $auth_uid,
                'review_id' => $review_id
            ])->exists();

            if ($already_liked) {
                return response()->json([
                    'status' => 666,
                    'message' => 'Already Liked'
                ]);
            } else {
                Like::create([
                    'user_id' => $auth_uid,
                    'review_id' => $review_id
                ]);
                
                return response()->json([
                    'status' => 202,
                    'message' => 'Request Created'
                ]);
            }
        } else {
            return response()->json([
                'status' => 616,
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
        
        if ($auth) {
            Like::where('review_id', $review_id)->firstOrFail()->delete();
            
            return response()->json([
                'status' => 404,
                'message' => 'Request Deleted'
            ]);
        } else {
            return response()->json([
                'status' => 616,
                'message' => 'Invalid Credentials'
            ]);
        }
    }
}
