<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $fillable = [
        'client_id', 'phone', 'name', 'email', 'role', 'profile_image', 'password', 'is_verified',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
    public function assessments()
    {
        return $this->hasMany(Assessment::class, 'user_id');
    }

}
