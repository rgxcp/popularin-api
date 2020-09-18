<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

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
        'is_self',
        'timestamp'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getUserAttribute()
    {
        return Auth::user();
    }

    public function getIsSelfAttribute()
    {
        $authID = Auth::check() ? Auth::id() : 0;

        return $this->user_id == $authID;
    }

    public function getTimestampAttribute()
    {
        return $this->created_at->diffForHumans();
    }
}
