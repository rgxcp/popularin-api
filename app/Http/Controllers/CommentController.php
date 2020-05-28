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
        Carbon::setLocale('id');

        $comments = Comment::with([
            'user'
        ])->where('review_id', $review_id)
          ->orderBy('created_at', 'asc')
          ->paginate(50);

        return response()->json([
            'status' => isset($comments[0]) ? 101 : 606,
            'message' => isset($comments[0]) ? 'Request Retrieved' : 'Request Not Found',
            'result' => isset($comments[0]) ? $comments : null
        ]);
    }

    public function create(Request $request) {
        Carbon::setLocale('id');

        $authID = $request->header('Auth-ID');
        $authToken = $request->header('Auth-Token');
        
        $isAuth = User::where([
            'id' => $authID,
            'token' => $authToken
        ])->exists();
        
        if ($isAuth) {
            $validator = Validator::make($request->all(), [
                'comment_detail' => 'required|max:300'
            ],[
                'required' => 'Komen harus di isi',
                'max' => 'Komen maksimal 300 karakter'
            ]);
    
            if ($validator->fails()) {
                return response()->json([
                    'status' => 626,
                    'message' => 'Validator Fails',
                    'result' => $validator->errors()->all()
                ]);
            } else {
                $comment = Comment::create([
                    'user_id' => $authID,
                    'review_id' => $request['review_id'],
                    'comment_detail' => $request['comment_detail'],
                    'comment_date' => Carbon::now('+07:00')->format('Y-m-d')
                ]);

                $user = User::select(
                    'id',
                    'username',
                    'profile_picture'
                )->findOrFail($authID);

                $collection = collect([
                    'comment' => $comment,
                    'user' => $user
                ]);
    
                return response()->json([
                    'status' => 202,
                    'message' => 'Request Created',
                    'result' => $collection
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
        $comment = Comment::findOrFail($id);
        $authToken = $request->header('Auth-Token');
        
        $isAuth = User::where([
            'id' => $comment->user_id,
            'token' => $authToken
        ])->exists();
        
        if ($isAuth) {
            $comment->delete();

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
