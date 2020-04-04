<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Comment extends Model
{
    use SoftDeletes;

    protected $fillable = [
        // Empty
    ];

    protected $guarded = [
        'user_id',
        'review_id',
        'comment_text',
        'comment_date'
    ];

    protected $hidden = [
        'deleted_at'
    ];
}
