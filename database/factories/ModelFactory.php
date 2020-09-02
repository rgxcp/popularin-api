<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Comment;
use App\CommentLike;
use App\Developer;
use App\Favorite;
use App\Film;
use App\Following;
use App\Point;
use App\Review;
use App\ReviewLike;
use App\User;
use App\Watchlist;
use Faker\Generator as Faker;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(Comment::class, function (Faker $faker) {
    return [
        'user_id' => $faker->numberBetween(1, 10),
        'review_id' => $faker->numberBetween(1, 10),
        'comment_detail' => $faker->paragraph(3),
        'comment_date' => $faker->date('Y-m-d')
    ];
});

$factory->define(CommentLike::class, function (Faker $faker) {
    return [
        'user_id' => $faker->numberBetween(1, 10),
        'comment_id' => $faker->numberBetween(1, 10)
    ];
});

$factory->define(Developer::class, function () {
    return [
        'full_name' => 'Developer',
        'username' => 'developer',
        'email' => 'developer@gmail.com',
        'profile_picture' => 'https://ui-avatars.com/api/?name=Developer&size=512',
        'password' => Hash::make('developer'),
        'api_key' => Hash('SHA256', Str::random(100)),
        'api_token' => Hash('SHA256', Str::random(100))
    ];
});

$factory->define(Favorite::class, function (Faker $faker) {
    return [
        'user_id' => $faker->numberBetween(1, 10),
        'tmdb_id' => 1
    ];
});

$factory->define(Film::class, function (Faker $faker) {
    return [
        'tmdb_id' => 1,
        'genre_id' => 1,
        'title' => 'Dua Garis Biru',
        'release_date' => $faker->date('Y-m-d'),
        'poster' => 'https://themoviedb.org/poster/Dua+Garis+Biru.jpg'
    ];
});

$factory->define(Following::class, function (Faker $faker) {
    return [
        'user_id' => $faker->numberBetween(1, 10),
        'following_id' => $faker->numberBetween(1, 10)
    ];
});

$factory->define(Point::class, function (Faker $faker) {
    $total = array(10, 30, 5);
    $type = array('FAVORITE', 'REVIEW', 'WATCHLIST');
    $index = array_rand($type);

    return [
        'user_id' => $faker->numberBetween(1, 10),
        'type_id' => $faker->numberBetween(1, 10),
        'total' => $total[$index],
        'type' => $type[$index]
    ];
});

$factory->define(Review::class, function (Faker $faker) {
    $rating = array(0.5, 1.0, 1.5, 2.0, 2.5, 3.0, 3.5, 4.0, 4.5, 5.0);
    $index = array_rand($rating);

    return [
        'user_id' => $faker->numberBetween(1, 10),
        'tmdb_id' => 1,
        'rating' => $rating[$index],
        'review_detail' => $faker->paragraph(5),
        'review_date' => $faker->date('Y-m-d'),
        'watch_date' => $faker->date('Y-m-d')
    ];
});

$factory->define(ReviewLike::class, function (Faker $faker) {
    return [
        'user_id' => $faker->numberBetween(1, 10),
        'review_id' => $faker->numberBetween(1, 10)
    ];
});

$factory->define(User::class, function (Faker $faker) {
    $fullName = $faker->name;
    $username = preg_replace('/\s+/', '', strtolower($fullName));

    return [
        'full_name' => $fullName,
        'username' => $username,
        'email' => $username . '@gmail.com',
        'profile_picture' => 'https://ui-avatars.com/api/?name=' . preg_replace('/\s+/', '+', $fullName) . '&size=512',
        'password' => Hash::make($username),
        'api_token' => Hash('SHA256', Str::random(100))
    ];
});

$factory->define(Watchlist::class, function (Faker $faker) {
    return [
        'user_id' => $faker->numberBetween(1, 10),
        'tmdb_id' => 1
    ];
});
