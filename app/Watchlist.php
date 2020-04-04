<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Watchlist extends Model
{
    use SoftDeletes;

    protected $fillable = [
        // Empty
    ];

    protected $guarded = [
        'user_id',
        'film_id'
    ];

    protected $hidden = [
        'deleted_at'
    ];
}
