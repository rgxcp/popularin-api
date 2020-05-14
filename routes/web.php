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

$router->group(['prefix' => 'api'], function () use ($router) {
    $router->get('/', function () use ($router) {
        return $router->app->version();
    });

    $router->get('status', 'DeveloperController@showStatus');

    $router->get('review/{review_id}/comments', 'CommentController@shows');
    $router->post('comment', 'CommentController@create');
    $router->delete('comment/{id}', 'CommentController@delete');

    $router->get('film/{film_id}/favorites/from/all', 'FavoriteController@showFavoritesFromAll');
    $router->get('film/{film_id}/favorites/from/following', 'FavoriteController@showFavoritesFromFollowing');
    $router->get('user/{user_id}/favorites', 'FavoriteController@shows');
    $router->post('favorite', 'FavoriteController@create');
    $router->delete('favorite/{film_id}', 'FavoriteController@delete');

    $router->get('film/{tmdb_id}', 'FilmController@show');
    $router->get('film/{tmdb_id}/self', 'FilmController@showSelf');
    // $router->post('film', 'FilmController@create');
    $router->put('film/{tmdb_id}', 'FilmController@update');
    $router->delete('film/{tmdb_id}', 'FilmController@delete');

    $router->get('user/{user_id}/followings', 'FollowingController@showFollowings');
    $router->get('user/{user_id}/followers', 'FollowingController@showFollowers');
    $router->get('user/{user_id}/mutuals', 'FollowingController@showMutuals');
    $router->post('user/{user_id}/follow', 'FollowingController@create');
    $router->delete('user/{user_id}/unfollow', 'FollowingController@delete');

    $router->get('review/{review_id}/likes/from/all', 'LikeController@showLikesFromAll');
    $router->get('review/{review_id}/likes/from/following', 'LikeController@showLikesFromFollowing');
    $router->post('review/{review_id}/like', 'LikeController@create');
    $router->delete('review/{review_id}/unlike', 'LikeController@delete');

    $router->get('film/{tmdb_id}/reviews/from/all', 'ReviewController@showFilmReviewsFromAll');
    $router->get('film/{tmdb_id}/reviews/from/following', 'ReviewController@showFilmReviewsFromFollowing');
    $router->get('film/{tmdb_id}/reviews/liked', 'ReviewController@showLikedReviews');
    $router->get('film/{tmdb_id}/reviews/self', 'ReviewController@showSelfReviews');
    $router->get('user/{user_id}/reviews', 'ReviewController@showUserReviews');
    $router->get('review/{id}', 'ReviewController@show');
    $router->get('reviews', 'ReviewController@shows');
    $router->post('review', 'ReviewController@create');
    $router->put('review/{id}', 'ReviewController@update');
    $router->delete('review/{id}', 'ReviewController@delete');

    $router->get('user/search', 'UserController@search');
    $router->get('user/self', 'UserController@self');
    $router->get('user/{id}', 'UserController@show');
    $router->post('user/signup', 'UserController@signup');
    $router->post('user/signin', 'UserController@signin');
    $router->post('user/signout', 'UserController@signout');
    $router->put('user/{id}', 'UserController@update');
    $router->put('user/{id}/password', 'UserController@updatePassword');
    $router->delete('user/{id}', 'UserController@delete');

    $router->get('film/{film_id}/watchlists/from/all', 'WatchlistController@showWatchlistsFromAll');
    $router->get('film/{film_id}/watchlists/from/following', 'WatchlistController@showWatchlistsFromFollowing');
    $router->get('user/{user_id}/watchlists', 'WatchlistController@shows');
    $router->post('watchlist', 'WatchlistController@create');
    $router->delete('watchlist/{film_id}', 'WatchlistController@delete');
});
