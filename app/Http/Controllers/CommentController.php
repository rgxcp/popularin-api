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
        ])->where('review_id', $review_id)->get();

        return response()
            ->json([
                'status' => 201,
                'message' => 'Success',
                'results' => $comments
            ]);
    }

    public function create(Request $request) {
        $match = User::select('token')
            ->where([
                'id' => $request['user_id'],
                'token' => $request['user_token']
            ])->exists();
        
        if ($match == true) {
            Comment::create([
                'user_id' => $request['user_id'],
                'review_id' => $request['review_id'],
                'comment_text' => $request['comment_text'],
                'comment_date' => Carbon::now()->format('Y-m-d')
            ]);

            return response()
                ->json([
                    'status' => 201,
                    'message' => 'Created'
                ]);
        } else {
            return response()
                ->json([
                    'status' => 403,
                    'message' => 'Invalid Token'
                ]);
        }
    }

    public function delete(Request $request, $id) {
        $match = User::select('token')
            ->where([
                'id' => $request['user_id'],
                'token' => $request['user_token']
            ])->exists();
        
        if ($match == true) {
            Comment::findOrFail($id)
                ->delete();

            return response()
                ->json([
                    'status' => 201,
                    'message' => 'Deleted'
                ]);
        } else {
            return response()
                ->json([
                    'status' => 403,
                    'message' => 'Invalid Token'
                ]);
        }
    }
}
