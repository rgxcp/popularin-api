<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->group(['prefix' => 'api', 'middleware' => 'is_developer'], function () use ($router) {
    $router->group(['middleware' => 'auth'], function () use ($router) {
        // Comment
        $router->post('comment', 'CommentController@create');
        $router->delete('comment/{id}', 'CommentController@delete');

        // Favorite
        $router->get('film/{tmdb_id}/favorites/from/following', 'FavoriteController@showsFilmFavoriteFromFollowing');
        $router->post('film/{tmdb_id}/favorite', 'FavoriteController@create');
        $router->delete('film/{tmdb_id}/unfavorite', 'FavoriteController@delete');

        // Film
        $router->get('film/{tmdb_id}/self', 'FilmController@showSelf');

        // Following
        $router->get('user/{user_id}/mutuals', 'FollowingController@showsMutual');
        $router->post('user/{user_id}/follow', 'FollowingController@create');
        $router->delete('user/{user_id}/unfollow', 'FollowingController@delete');

        // Like
        $router->get('review/{review_id}/likes/from/following', 'LikeController@showsLikeFromFollowing');
        $router->post('review/{review_id}/like', 'LikeController@create');
        $router->delete('review/{review_id}/unlike', 'LikeController@delete');

        // Review
        $router->get('film/{tmdb_id}/reviews/from/following', 'ReviewController@showsFilmReviewFromFollowing');
        $router->get('film/{tmdb_id}/reviews/liked', 'ReviewController@showsLikedReview');
        $router->get('film/{tmdb_id}/reviews/self', 'ReviewController@showsSelfReview');
        $router->get('review/timeline', 'ReviewController@showsTimeline');
        $router->post('review', 'ReviewController@create');
        $router->put('review/{id}', 'ReviewController@update');
        $router->delete('review/{id}', 'ReviewController@delete');

        // User
        $router->get('user/self', 'UserController@showSelf');
        $router->post('user/signout', 'UserController@signOut');
        $router->put('user/{id}', 'UserController@updateProfile');
        $router->put('user/{id}/password', 'UserController@updatePassword');

        // Watchlist
        $router->get('film/{tmdb_id}/watchlists/from/following', 'WatchlistController@showsFilmWatchlistFromFollowing');
        $router->post('film/{tmdb_id}/watchlist', 'WatchlistController@create');
        $router->delete('film/{tmdb_id}/unwatchlist', 'WatchlistController@delete');
    });
    
    // Version
    $router->get('/', function () use ($router) {
        return $router->app->version();
    });

    // Comment
    $router->get('review/{review_id}/comments', 'CommentController@shows');

    // Favorite
    $router->get('film/{tmdb_id}/favorites/from/all', 'FavoriteController@showsFilmFavoriteFromAll');
    $router->get('user/{user_id}/favorites', 'FavoriteController@showsUserFavorite');

    // Film
    $router->get('film/{tmdb_id}', 'FilmController@show');

    // Following
    $router->get('user/{user_id}/followings', 'FollowingController@showsFollowing');
    $router->get('user/{user_id}/followers', 'FollowingController@showsFollower');

    // Like
    $router->get('review/{review_id}/likes/from/all', 'LikeController@showsLikeFromAll');

    // Review
    $router->get('film/{tmdb_id}/reviews/from/all', 'ReviewController@showsFilmReviewFromAll');
    $router->get('user/{user_id}/reviews', 'ReviewController@showsUserReview');
    $router->get('review/{id}', 'ReviewController@show');
    $router->get('reviews', 'ReviewController@shows');

    // User
    $router->get('user/search/{query}', 'UserController@search');
    $router->get('user/{id}', 'UserController@show');
    $router->post('user/signup', 'UserController@signUp');
    $router->post('user/signin', 'UserController@signIn');

    // Watchlist
    $router->get('film/{tmdb_id}/watchlists/from/all', 'WatchlistController@showsFilmWatchlistFromAll');
    $router->get('user/{user_id}/watchlists', 'WatchlistController@showsUserWatchlist');
});

$router->group(['prefix' => 'developer'], function () use ($router) {
    $router->get('status', 'DeveloperController@showStatus');
    $router->post('signup', 'DeveloperController@signUp');
});
