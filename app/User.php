<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Lumen\Auth\Authorizable;

class User extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable, SoftDeletes;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [
        // All columns are guarded
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    // public function favorites() {
    //     return $this->hasManyThrough(Favorite::class, Film::class, 'tmdb_id', 'tmdb_id', 'tmdb_id')->orderBy('created_at', 'desc')->take(5);
    // }
    
    // public function reviews() {
    //     return $this->hasMany(Review::class)->orderBy('created_at', 'desc')->take(5);
    // }
}
