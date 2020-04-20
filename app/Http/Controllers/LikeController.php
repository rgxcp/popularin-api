<?php

namespace App\Http\Controllers;

use App\Like;
use App\Review;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LikeController extends Controller
{
    public function shows(Request $request, $review_id) {
        $likes = Like::with([
            'user'
        ])->where('review_id', $review_id)
          ->orderBy('created_at', 'desc')
          ->paginate(30);
        
        return response()->json([
            'status' => isset($likes[0]) ? 000 : 000,
            'message' => isset($likes[0]) ? 'Likes Retrieved' : 'Empty Likes',
            'result' => isset($likes[0]) ? $likes : null
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
            $already_liked = Review::where([
                'user_id' => $auth_uid,
                'review_id' => $request['review_id']
            ])->exists();

            if ($already_liked == true) {
                return response()->json([
                    'status' => 000,
                    'message' => 'Review Already Liked'
                ]);
            } else {
                $validator = Validator::make($request->all(), [
                    'review_id' => 'required|integer'
                ]);
        
                if ($validator->fails()) {
                    return response()->json([
                        'status' => 999,
                        'message' => 'Validator Fails',
                        'result' => $validator->errors()->all()
                    ]);
                } else {
                    Favorite::create([
                        'user_id' => $auth_uid,
                        'review_id' => $request['review_id']
                    ]);
                    
                    return response()->json([
                        'status' => 502,
                        'message' => 'Review Liked'
                    ]);
                }
            }
        } else {
            return response()->json([
                'status' => 808,
                'message' => 'Invalid Credentials'
            ]);
        }
    }

    public function delete(Request $request, $id) {
        $auth_uid = Like::select('user_id')->where('id', $id)->firstOrFail();
        $auth_token = $request->header('auth_token');
        
        $auth = User::where([
            'id' => $auth_uid['user_id'],
            'token' => $auth_token
        ])->exists();
        
        if ($auth == true) {
            Like::findOrFail($id)->delete();
            
            return response()->json([
                'status' => 000,
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
