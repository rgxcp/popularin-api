<?php

namespace App\Http\Controllers;

use App\Comment;
use App\CommentReport;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class CommentReportController extends Controller
{
    public function index($comment_id)
    {
        Carbon::setLocale('id');

        $commentReports = CommentReport::with([
            'reportCategory', 'user'
        ])->where('comment_id', $comment_id)
            ->orderBy('created_at', 'asc')
            ->paginate(20);

        return response()->json([
            'status' => isset($commentReports[0]) ? 101 : 606,
            'message' => isset($commentReports[0]) ? 'Request Retrieved' : 'Request Not Found',
            'result' => isset($commentReports[0]) ? $commentReports : null
        ]);
    }

    public function store(Request $request, $comment_id)
    {
        $comment = Comment::withCount('commentReports')->findOrFail($comment_id);

        $authID = Auth::id();

        if ($comment['user_id'] == $authID) {
            return response()->json([
                'status' => 686,
                'message' => 'Can\'t Report Self'
            ]);
        } else {
            CommentReport::firstOrCreate([
                'user_id' => $authID,
                'comment_id' => $comment_id,
                'report_category_id' => $request['report_category_id']
            ]);

            if ($comment['comment_reports_count'] + 1 == env('NSFW_THRESHOLD')) {
                $comment->delete();
            }

            return response()->json([
                'status' => 202,
                'message' => 'Request Created'
            ]);
        }
    }
}
