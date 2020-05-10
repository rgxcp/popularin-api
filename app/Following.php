<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Following extends Model
{
    protected $guarded = [
        // All columns are guarded
    ];

    protected $hidden = [
        'user_id',
        'following_id',
        'created_at',
        'updated_at'
    ];

    public function following() {
        return $this->belongsTo(User::class, 'following_id', 'id')->select([
            'id',
            'full_name',
            'username',
            'profile_picture'
        ])->withTrashed();
    }

    public function follower() {
        return $this->belongsTo(User::class, 'user_id', 'id')->select([
            'id',
            'full_name',
            'username',
            'profile_picture'
        ])->withTrashed();
    }
}
