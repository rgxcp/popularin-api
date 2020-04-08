<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Following extends Model
{
    protected $guarded = [
        // All columns are guarded
    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];
}
