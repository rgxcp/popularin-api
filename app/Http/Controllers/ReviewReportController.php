<?php

namespace App\Http\Controllers;

use App\Review;
use App\ReviewReport;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class ReviewReportController extends Controller
{
    public function index($review_id)
    {
        Carbon::setLocale('id');

        $reviewReports = ReviewReport::with([
            'reportCategory', 'user'
        ])->where('review_id', $review_id)
            ->orderBy('created_at', 'asc')
            ->paginate(20);

        return response()->json([
            'status' => isset($reviewReports[0]) ? 101 : 606,
            'message' => isset($reviewReports[0]) ? 'Request Retrieved' : 'Request Not Found',
            'result' => isset($reviewReports[0]) ? $reviewReports : null
        ]);
    }

    public function store(Request $request, $review_id)
    {
        $review = Review::findOrFail($review_id);

        $authID = Auth::id();

        if ($review['user_id'] == $authID) {
            return response()->json([
                'status' => 686,
                'message' => 'Can\'t Report Self'
            ]);
        } else {
            ReviewReport::firstOrCreate([
                'user_id' => $authID,
                'review_id' => $review_id,
                'report_category_id' => $request['report_category_id']
            ]);

            if ($review['total_report'] == env('NSFW_THRESHOLD')) {
                $review->delete();
            }

            return response()->json([
                'status' => 202,
                'message' => 'Request Created'
            ]);
        }
    }
}
