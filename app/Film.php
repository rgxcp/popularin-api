<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Film extends Model
{
    protected $guarded = [
        // All columns are guarded
    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];
}
