<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Review extends Model
{
    use SoftDeletes;

    protected $guarded = [
        // All columns are guarded
    ];

    protected $hidden = [
        'user_id',
        'tmdb_id',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    public function film() {
        return $this->belongsTo(Film::class, 'tmdb_id', 'tmdb_id');
    }

    public function user() {
        return $this->belongsTo(User::class)->select([
            'id',
            'first_name',
            'full_name',
            'username',
            'profile_picture'
        ])->withTrashed();
    }
}
