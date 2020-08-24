<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Watchlist extends Model
{
    protected $guarded = [
        // All columns are guarded
    ];

    protected $hidden = [
        'user_id',
        'tmdb_id',
        'created_at',
        'updated_at'
    ];

    public function film()
    {
        return $this->belongsTo(Film::class, 'tmdb_id', 'tmdb_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
