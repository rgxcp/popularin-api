<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Review extends Model
{
    use SoftDeletes;

    protected $fillable = [
        // Empty
    ];

    protected $guarded = [
        'user_id',
        'film_id',
        'rating',
        'review_text',
        'review_date'
    ];

    protected $hidden = [
        'deleted_at'
    ];
}
