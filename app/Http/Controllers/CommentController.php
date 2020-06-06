<?php

namespace App\Http\Controllers;

use Auth;
use App\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Gate;
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
                'user_id' => Auth::id(),
                'review_id' => $request['review_id'],
                'comment_detail' => $request['comment_detail'],
                'comment_date' => Carbon::now('+07:00')->format('Y-m-d')
            ]);

            return response()->json([
                'status' => 202,
                'message' => 'Request Created',
                'result' => $comment->append('user')
            ]);
        }
    }

    public function delete($id) {
        $comment = Comment::findOrFail($id);

        if (Gate::allows('delete-comment', $comment)) {
            $comment->delete();

            return response()->json([
                'status' => 404,
                'message' => 'Request Deleted'
            ]);
        } else {
            return response()->json([
                'status' => 939,
                'message' => 'Unauthorized'
            ]);
        }
    }
}
