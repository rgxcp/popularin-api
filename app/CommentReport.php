<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CommentReport extends Model
{
    protected $guarded = [
        // All columns are guarded
    ];

    protected $hidden = [
        'comment_id',
        'report_category_id',
        'created_at',
        'updated_at'
    ];

    protected $appends = [
        'timestamp'
    ];

    public function reportCategory()
    {
        return $this->belongsTo(ReportCategory::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getTimestampAttribute()
    {
        return $this->created_at->diffForHumans();
    }
}
