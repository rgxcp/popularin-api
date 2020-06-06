<?php

namespace App;

use Auth;
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

    protected $append = [
        'user'
    ];

    protected $appends = [
        'timestamp'
    ];

    public function user() {
        return $this->belongsTo(User::class)->select([
            'id',
            'username',
            'profile_picture'
        ])->withTrashed();
    }

    public function getUserAttribute() {
        return Auth::user()->only([
            'id',
            'username',
            'profile_picture'
        ]);
    }

    public function getTimestampAttribute() {
        return $this->created_at->diffForHumans();
    }
}
