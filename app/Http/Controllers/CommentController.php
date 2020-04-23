<?php

namespace App\Http\Controllers;

use App\Comment;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;

class CommentController extends Controller
{
    public function shows($review_id) {
        $comments = Comment::with([
            'user'
        ])->where('review_id', $review_id)
          ->orderBy('created_at', 'asc')
          ->paginate(30);

        return response()->json([
            'status' => isset($comments[0]) ? 101 : 606,
            'message' => isset($comments[0]) ? 'Request Retrieved' : 'Request Not Found',
            'result' => isset($comments[0]) ? $comments : null
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
                'review_id' => 'required|integer',
                'comment_text' => 'required|string'
            ]);
    
            if ($validator->fails()) {
                return response()->json([
                    'status' => 626,
                    'message' => 'Validator Fails',
                    'result' => $validator->errors()->all()
                ]);
            } else {
                Comment::create([
                    'user_id' => $auth_uid,
                    'review_id' => $request['review_id'],
                    'comment_text' => $request['comment_text'],
                    'comment_date' => Carbon::now()->format('Y-m-d')
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

    public function delete(Request $request, $id) {
        $auth_uid = Comment::select('user_id')->where('id', $id)->firstOrFail();
        $auth_token = $request->header('auth_token');
        
        $auth = User::where([
            'id' => $auth_uid['user_id'],
            'token' => $auth_token
        ])->exists();
        
        if ($auth == true) {
            Comment::findOrFail($id)->delete();

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
