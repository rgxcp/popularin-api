<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Film extends Model
{
    use SoftDeletes;

    protected $fillable = [
        // Empty
    ];

    protected $guarded = [
        'tmdb_id',
        'title',
        'year',
        'poster',
        'overview',
        'contributor'
    ];

    protected $hidden = [
        'deleted_at'
    ];
}
