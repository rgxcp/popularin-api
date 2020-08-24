<?php

namespace App\Http\Traits;

use App\Film;

trait FilmTrait
{
    public function addFilm($tmdb_id)
    {
        $url = "https://api.themoviedb.org/3/movie/" . $tmdb_id . "?api_key=" . env('TMDB_API_KEY');
        $json = file_get_contents($url);
        $data = json_decode($json, true);

        Film::create([
            'tmdb_id' => $tmdb_id,
            'genre_id' => $data['genres'][0]['id'],
            'title' => $data['original_title'],
            'release_date' => $data['release_date'],
            'poster' => $data['poster_path']
        ]);
    }
}
