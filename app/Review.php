<?php

namespace App;

use Auth;
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

    public $appends = [
        'timestamp',
        'is_liked',
        'total_comment',
        'total_like'
    ];

    public function film()
    {
        return $this->belongsTo(Film::class, 'tmdb_id', 'tmdb_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getTimestampAttribute()
    {
        return $this->created_at->diffForHumans(null, true);
    }

    public function getIsLikedAttribute()
    {
        $authID = Auth::check() ? Auth::id() : 0;

        return ReviewLike::where(['user_id' => $authID, 'review_id' => $this->id])->exists();
    }

    public function getTotalCommentAttribute()
    {
        return Comment::where('review_id', $this->id)->count();
    }

    public function getTotalLikeAttribute()
    {
        return ReviewLike::where('review_id', $this->id)->count();
    }
}
