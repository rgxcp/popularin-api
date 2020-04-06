<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Film extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'tmdb_id',
        'title',
        'release_date',
        'poster',
        'overview',
        'contributor'
    ];

    /*
     * protected $guarded = [
     *     // Empty
     * ];
     */

    protected $hidden = [
        'contributor',
        'deleted_at'
    ];

    public function contributor_info() {
        return $this->belongsTo(User::class, 'contributor', 'id')->select([
            'id',
            'first_name',
            'profile_picture'
        ]);
    }

    public function total_reviews() {
        return $this->hasMany(Review::class, 'tmdb_id', 'tmdb_id');
    }
}
