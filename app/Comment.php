<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Comment extends Model
{
    use SoftDeletes;

    protected $guarded = [
        // All columns are guarded
    ];

    protected $hidden = [
        'user_id',
        'review_id',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    public function user() {
        return $this->belongsTo(User::class)->select([
            'id',
            'first_name',
            'profile_picture'
        ]);
    }
}
