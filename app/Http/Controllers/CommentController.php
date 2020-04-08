<?php

namespace App\Http\Controllers;

use App\Comment;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class CommentController extends Controller
{
    public function shows($review_id) {
        $comments = Comment::with([
            'user'
        ])->where('review_id', $review_id)
          ->orderBy('created_at', 'desc')
          ->paginate(30);

        return response()
            ->json([
                'status' => 101,
                'message' => 'Comments Retrieved',
                'total_comments' => Comment::where('review_id', $review_id)->count(),
                'results' => $comments
            ]);
    }

    public function create(Request $request) {
        $auth = User::where([
            'id' => $request['user_id'],
            'token' => $request['user_token']
        ])->exists();
        
        if ($auth == true) {
            Comment::create([
                'user_id' => $request['user_id'],
                'review_id' => $request['review_id'],
                'comment_text' => $request['comment_text'],
                'comment_date' => Carbon::now()->format('Y-m-d')
            ]);

            return response()
                ->json([
                    'status' => 202,
                    'message' => 'Comment Created'
                ]);
        } else {
            return response()
                ->json([
                    'status' => 505,
                    'message' => 'Not Authorized to Create Comment'
                ]);
        }
    }

    public function delete(Request $request, $id) {
        $user_id = Comment::select('user_id')
            ->where([
                'id' => $id
            ])->firstOrFail();

        $auth = User::where([
            'id' => $user_id['user_id'],
            'token' => $request['user_token']
        ])->exists();
        
        if ($auth == true) {
            Comment::findOrFail($id)
                ->delete();

            return response()
                ->json([
                    'status' => 404,
                    'message' => 'Comment Deleted'
                ]);
        } else {
            return response()
                ->json([
                    'status' => 505,
                    'message' => 'Not Authorized to Delete Comment'
                ]);
        }
    }
}
