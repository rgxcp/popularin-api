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
            'first_name',
            'profile_picture'
        ]);
    }

    public function follower() {
        return $this->belongsTo(User::class, 'user_id', 'id')->select([
            'id',
            'first_name',
            'profile_picture'
        ]);
    }
}
