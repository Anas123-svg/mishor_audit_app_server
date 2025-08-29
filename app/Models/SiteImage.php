<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class SiteImage extends Model
{
    use HasFactory;

    protected $fillable = ['assessment_id', 'site_image', 'is_flagged'];

    public function assessment()
    {
        return $this->belongsTo(Assessment::class, 'assessment_id');
    }
}
