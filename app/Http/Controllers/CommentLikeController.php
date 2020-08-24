<?php

namespace App\Http\Controllers;

use App\Comment;
use App\CommentLike;
use App\Following;
use Illuminate\Support\Facades\Auth;

class CommentLikeController extends Controller
{
    public function showsCommentLikeFromAll($comment_id)
    {
        $commentLikes = CommentLike::with([
            'user'
        ])->where('comment_id', $comment_id)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'status' => isset($commentLikes[0]) ? 101 : 606,
            'message' => isset($commentLikes[0]) ? 'Request Retrieved' : 'Request Not Found',
            'result' => isset($commentLikes[0]) ? $commentLikes : null
        ]);
    }

    public function showsCommentLikeFromFollowing($comment_id)
    {
        $followings = Following::select('following_id')->where('user_id', Auth::id());

        $commentLikes = CommentLike::with([
            'user'
        ])->where('comment_id', $comment_id)
            ->whereIn('user_id', $followings)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'status' => isset($commentLikes[0]) ? 101 : 606,
            'message' => isset($commentLikes[0]) ? 'Request Retrieved' : 'Request Not Found',
            'result' => isset($commentLikes[0]) ? $commentLikes : null
        ]);
    }

    public function create($comment_id)
    {
        Comment::findOrFail($comment_id);

        $authID = Auth::id();

        $isLiked = CommentLike::where([
            'user_id' => $authID,
            'comment_id' => $comment_id
        ])->exists();

        if ($isLiked) {
            return response()->json([
                'status' => 666,
                'message' => 'Already Liked'
            ]);
        } else {
            CommentLike::create([
                'user_id' => $authID,
                'comment_id' => $comment_id
            ]);

            return response()->json([
                'status' => 202,
                'message' => 'Request Created'
            ]);
        }
    }

    public function delete($comment_id)
    {
        CommentLike::where([
            'user_id' => Auth::id(),
            'comment_id' => $comment_id
        ])->firstOrFail()
            ->delete();

        return response()->json([
            'status' => 404,
            'message' => 'Request Deleted'
        ]);
    }
}
