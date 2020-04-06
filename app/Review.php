<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Review extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    protected $hidden = [
        'deleted_at'
    ];

    public function film() {
        return $this->belongsTo(Film::class, 'tmdb_id', 'tmdb_id');
    }

    // public function reviews() {
    //     return $this->hasMany(Review::class, 'movies_id', 'id')->orderBy('created_at', 'desc');
    // }

    public function user() {
        return $this->belongsTo(User::class)->select([
            'id',
            'first_name',
            'profile_picture'
        ]);
    }
}
