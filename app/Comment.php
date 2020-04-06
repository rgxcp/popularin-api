<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Comment extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'review_id',
        'comment_text',
        'comment_date'
    ];

    /*
     * protected $guarded = [
     *     // Empty
     * ];
     */

    protected $hidden = [
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
