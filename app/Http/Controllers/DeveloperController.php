<?php

namespace App\Http\Controllers;

class DeveloperController extends Controller
{
    public function showStatus() {
        return response()->json([
            101 => 'Film Retrieved',
            102 => 'Film Added',
            103 => 'Film Overview Updated',
            104 => 'Film Overview Removed',
            121 => 'Self Film Retrieved',
            201 => 'User Retrieved',
            202 => 'User Signed Up',
            203 => 'User Updated',
            204 => 'User Deleted',
            211 => 'Users Retrieved',
            221 => 'Self Retrieved',
            212 => 'User Signed In',
            232 => 'User Signed Out',
            301 => 'Review Retrieved',
            302 => 'Review Added',
            303 => 'Review Updated',
            304 => 'Review Deleted',
            311 => 'Reviews Retrieved',
            321 => 'Film Reviews Retrieved',
            331 => 'User Reviews Retrieved',
            371 => 'Following Reviews Retrieved',
            401 => 'Comments Retrieved',
            402 => 'Comment Added',
            404 => 'Comment Deleted',
            501 => 'Likes Retrieved',
            502 => 'Review Liked',
            504 => 'Review Unliked',
            601 => 'Favorites Retrieved',
            602 => 'Favorite Added',
            604 => 'Favorite Removed',
            
            701 => 'Watchlists Retrieved',
            702 => 'Watchlist Added',
            704 => 'Watchlist Removed',
            801 => 'Followings Retrieved',
            802 => 'User Followed',
            804 => 'User Unfollowed',
            811 => 'Followers Retrieved',
            808 => 'Invalid Credentials',
            908 => 'Can\'t Follow Self',
            909 => 'Request Not Found',
            919 => 'Empty Film Reviews',
            928 => 'Already in Favorite',
            928 => 'Already in Watchlist',
            928 => 'User Already Followed',
            929 => 'Empty User Reviews',
            939 => 'Empty Following Reviews',
            949 => 'Empty Comments',
            989 => 'Empty Favorites',
            989 => 'Empty Watchlists',
            989 => 'Empty Followings',
            989 => 'Empty Followers',
            999 => 'Validator Fails',
            959 => 'Empty Likes',
            925 => 'Review Already Liked'
        ]);
    }
}
