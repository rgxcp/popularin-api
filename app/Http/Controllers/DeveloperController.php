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
            211 => 'Self Retrieved',
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
            501 => 'Favorites Retrieved',
            502 => 'Favorite Added',
            504 => 'Favorite Removed',
            601 => 'Watchlists Retrieved',
            602 => 'Watchlist Added',
            604 => 'Watchlist Removed',
            701 => 'Followings Retrieved',
            702 => 'User Followed',
            704 => 'User Unfollowed',
            711 => 'Followers Retrieved',
            808 => 'Invalid Credentials',
            907 => 'Can\'t Follow Self',
            909 => 'Request Not Found',
            919 => 'Empty Film Reviews',
            925 => 'Already in Favorite',
            926 => 'Already in Watchlist',
            927 => 'User Already Followed',
            929 => 'Empty User Reviews',
            939 => 'Empty Following Reviews',
            949 => 'Empty Comments',
            959 => 'Empty Favorites',
            969 => 'Empty Watchlists',
            979 => 'Empty Followings',
            989 => 'Empty Followers',
            999 => 'Validator Fails'
        ]);
    }
}
