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
        'is_positive',
        'description',
        'timestamp'
    ];

    public function getTotalAttribute($total)
    {
        return $total > 0 ? "+$total" : $total;
    }

    public function getIsPositiveAttribute()
    {
        return $this->total > 0;
    }

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
                $review = Review::select('tmdb_id')->where('id', $this->type_id)->first();
                $filmID = $review['tmdb_id'];
                $film = Film::select('title', 'release_date')->where('tmdb_id', $filmID)->first();
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
