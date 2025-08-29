<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class Assessment extends Model
{
    use HasFactory;
    use HasApiTokens;


    protected $fillable = ['client_id', 'template_id', 'user_id', 'assessment', 'status','name', 'location','submited_to_admin','feedback_by_admin','status_by_admin','complete_by_user'];
    protected $casts = [
        'assessment' => 'array',  
    ];


    public function client()
    {
        return $this->belongsTo(Client::class);
    }


    public function template()
    {
        return $this->belongsTo(Template::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function siteImages()
    {
        return $this->hasMany(SiteImage::class);
    }


}
