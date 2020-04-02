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

    $router->get('film/{film_id}/review/{review_id}/comments', 'CommentController@shows');
    $router->post('comment', 'CommentController@create');
    $router->put('comment/{id}', 'CommentController@update');
    $router->delete('comment/{id}', 'CommentController@delete');

    $router->get('user/{user_id}/favorites', 'FavoriteController@shows');
    $router->post('favorite', 'FavoriteController@create');
    $router->put('favorite/{id}', 'FavoriteController@update');
    $router->delete('favorite/{id}', 'FavoriteController');

    $router->get('film/{id}', 'FilmController@show');
    $router->get('films', 'FilmController@shows');
    $router->post('film', 'FilmController@create');
    $router->put('film/{id}', 'FilmController@update');
    $router->delete('film/{id}', 'FilmController');

    $router->get('film/{film_id}/review/{id}', 'ReviewController@showReview');
    $router->get('film/{film_id}/reviews', 'ReviewController@showReviews');
    $router->get('reviews', 'ReviewController@shows');
    $router->post('review', 'ReviewController@create');
    $router->put('review/{id}', 'ReviewController@update');
    $router->delete('review/{id}', 'ReviewController');

    $router->get('user/{id}', 'UserController@show');
    $router->post('user/signin', 'UserController@create');
    $router->post('user/signup', 'UserController@create');
    $router->post('user/signout', 'UserController@create');
    $router->put('user/{id}', 'UserController@update');
    $router->delete('user/{id}', 'UserController');

    $router->get('user/{id}/watchlists', 'WatchlistController@shows');
    $router->post('watchlist', 'WatchlistController@create');
    $router->put('watchlist/{id}', 'WatchlistController@update');
    $router->delete('watchlist/{id}', 'WatchlistController');
});
