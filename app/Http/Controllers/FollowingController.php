<?php

namespace App\Http\Controllers;

use Auth;
use App\Following;
use App\User;

class FollowingController extends Controller
{
    public function showsFollowing($user_id)
    {
        $followings = Following::with([
            'following'
        ])->where('user_id', $user_id)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'status' => isset($followings[0]) ? 101 : 606,
            'message' => isset($followings[0]) ? 'Request Retrieved' : 'Request Not Found',
            'result' => isset($followings[0]) ? $followings : null
        ]);
    }

    public function showsFollower($user_id)
    {
        $followers = Following::with([
            'follower'
        ])->where('following_id', $user_id)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'status' => isset($followers[0]) ? 101 : 606,
            'message' => isset($followers[0]) ? 'Request Retrieved' : 'Request Not Found',
            'result' => isset($followers[0]) ? $followers : null
        ]);
    }

    public function showsMutual($user_id)
    {
        $authFollowings = Following::select('following_id')->where('user_id', Auth::id())->pluck('following_id')->toArray();
        $userFollowings = Following::select('following_id')->where('user_id', $user_id)->pluck('following_id')->toArray();
        $intersectFollowings = array_values(array_intersect($authFollowings, $userFollowings));

        $mutuals = User::whereIn('id', $intersectFollowings)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'status' => isset($mutuals[0]) ? 101 : 606,
            'message' => isset($mutuals[0]) ? 'Request Retrieved' : 'Request Not Found',
            'result' => isset($mutuals[0]) ? $mutuals : null
        ]);
    }

    public function create($user_id)
    {
        User::findOrFail($user_id);

        $authID = Auth::id();

        $isFollowed = Following::where([
            'user_id' => $authID,
            'following_id' => $user_id
        ])->exists();

        if ($user_id == $authID) {
            return response()->json([
                'status' => 636,
                'message' => 'Can\'t Follow Self'
            ]);
        } else if ($isFollowed) {
            return response()->json([
                'status' => 656,
                'message' => 'Already Followed'
            ]);
        } else {
            Following::create([
                'user_id' => $authID,
                'following_id' => $user_id
            ]);

            return response()->json([
                'status' => 202,
                'message' => 'Request Created'
            ]);
        }
    }

    public function delete($user_id)
    {
        Following::where([
            'user_id' => Auth::id(),
            'following_id' => $user_id
        ])->firstOrFail()
            ->delete();

        return response()->json([
            'status' => 404,
            'message' => 'Request Deleted'
        ]);
    }
}
