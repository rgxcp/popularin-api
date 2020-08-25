<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Point extends Model
{
    protected $guarded = [
        // All columns are guarded
    ];

    protected $hidden = [
        'user_id',
        'created_at',
        'updated_at'
    ];

    public $appends = [
        'description',
        'timestamp'
    ];

    public function getDescriptionAttribute()
    {
        $type = $this->type;

        switch ($type) {
            case 'FAVORITE':
                $film = Film::select('title', 'release_date')->where('tmdb_id', $this->type_id)->first();
                $filmTitle = $film['title'];
                $filmYear = substr($film['release_date'], 0, 4);
                return "Memfavoritkan film $filmTitle ($filmYear).";
                break;
            case 'REVIEW':
                $film = Film::select('title', 'release_date')->where('tmdb_id', $this->type_id)->first();
                $filmTitle = $film['title'];
                $filmYear = substr($film['release_date'], 0, 4);
                return "Mengulas film $filmTitle ($filmYear).";
                break;
            case 'WATCHLIST':
                $film = Film::select('title', 'release_date')->where('tmdb_id', $this->type_id)->first();
                $filmTitle = $film['title'];
                $filmYear = substr($film['release_date'], 0, 4);
                return "Ingin menonton film $filmTitle ($filmYear).";
                break;
            default:
                return "Tidak ada keterangan.";
        }
    }

    public function getTimestampAttribute()
    {
        return $this->created_at->diffForHumans(null, true, true);
    }
}
