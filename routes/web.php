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
    // Version
    $router->get('/', function () use ($router) {
        return $router->app->version();
    });

    // Comment
    $router->get('review/{review_id}/comments', 'CommentController@shows');

    // Favorite
    $router->get('film/{tmdb_id}/favorites/from/all', 'FavoriteController@showFilmFavoritesFromAll');
    $router->get('user/{user_id}/favorites', 'FavoriteController@showUserFavorites');

    // Film
    $router->get('film/{tmdb_id}', 'FilmController@show');

    // Following
    $router->get('user/{user_id}/followings', 'FollowingController@showFollowings');
    $router->get('user/{user_id}/followers', 'FollowingController@showFollowers');

    // Like
    $router->get('review/{review_id}/likes/from/all', 'LikeController@showLikesFromAll');

    // Review
    $router->get('film/{tmdb_id}/reviews/from/all', 'ReviewController@showFilmReviewsFromAll');
    $router->get('user/{user_id}/reviews', 'ReviewController@showUserReviews');
    $router->get('reviews', 'ReviewController@shows');

    // User
    $router->get('user/search/{query}', 'UserController@search');
    $router->post('user/signup', 'UserController@signup');
    $router->post('user/signin', 'UserController@signin');

    // Watchlist
    $router->get('film/{tmdb_id}/watchlists/from/all', 'WatchlistController@showFilmWatchlistsFromAll');
    $router->get('user/{user_id}/watchlists', 'WatchlistController@showUserWatchlists');

    $router->group(['middleware' => 'auth'], function () use ($router) {
        // Comment
        $router->post('comment', 'CommentController@create');
        $router->delete('comment/{id}', 'CommentController@delete');

        // Favorite
        $router->get('film/{tmdb_id}/favorites/from/following', 'FavoriteController@showFilmFavoritesFromFollowing');
        $router->post('film/{tmdb_id}/favorite', 'FavoriteController@create');
        $router->delete('film/{tmdb_id}/unfavorite', 'FavoriteController@delete');

        // Film
        $router->get('film/{tmdb_id}/self', 'FilmController@self');

        // Following
        $router->get('user/{user_id}/mutuals', 'FollowingController@showMutuals');
        $router->post('user/{user_id}/follow', 'FollowingController@create');
        $router->delete('user/{user_id}/unfollow', 'FollowingController@delete');

        // Like
        $router->get('review/{review_id}/likes/from/following', 'LikeController@showLikesFromFollowing');
        $router->post('review/{review_id}/like', 'LikeController@create');
        $router->delete('review/{review_id}/unlike', 'LikeController@delete');

        // Review
        $router->get('film/{tmdb_id}/reviews/from/following', 'ReviewController@showFilmReviewsFromFollowing');
        $router->get('film/{tmdb_id}/reviews/liked', 'ReviewController@showLikedReviews');
        $router->get('film/{tmdb_id}/reviews/self', 'ReviewController@showSelfReviews');
        $router->get('review/timeline', 'ReviewController@showTimeline');
        $router->get('review/{id}', 'ReviewController@show');
        $router->post('review', 'ReviewController@create');
        $router->put('review/{id}', 'ReviewController@update');
        $router->delete('review/{id}', 'ReviewController@delete');

        // User
        $router->get('user/self', 'UserController@self');
        $router->get('user/{id}', 'UserController@show');
        $router->post('user/signout', 'UserController@signout');
        $router->put('user/{id}', 'UserController@update');
        $router->put('user/{id}/password', 'UserController@updatePassword');

        // Watchlist
        $router->get('film/{tmdb_id}/watchlists/from/following', 'WatchlistController@showFilmWatchlistsFromFollowing');
        $router->post('film/{tmdb_id}/watchlist', 'WatchlistController@create');
        $router->delete('film/{tmdb_id}/unwatchlist', 'WatchlistController@delete');
    });
});

$router->group(['prefix' => 'developer'], function () use ($router) {
    $router->get('status', 'DeveloperController@showstatus');
    $router->get('self', 'DeveloperController@self');
    $router->post('signup', 'DeveloperController@signup');
    $router->post('signin', 'DeveloperController@signin');
    $router->post('signout', 'DeveloperController@signout');
    $router->put('{id}', 'DeveloperController@update');
    $router->put('{id}/password', 'DeveloperController@updatePassword');
});
