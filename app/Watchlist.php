<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Watchlist extends Model
{
    protected $fillable = [
        'user_id',
        'tmdb_id'
    ];

    /*
     * protected $guarded = [
     *     // Empty
     * ];
     */

    protected $hidden = [
        'deleted_at'
    ];

    public function film() {
        return $this->belongsTo(Film::class, 'tmdb_id', 'tmdb_id');
    }
}
