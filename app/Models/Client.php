<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Client extends Authenticatable
{
    use Notifiable, HasApiTokens;

    protected $fillable = [
        'name', 'email', 'profile_image', 'password','is_verified'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];
    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function assessments()
    {
        return $this->hasMany(Assessment::class);
    }
    public function ClientTemplate()
    {
        return $this->hasMany(ClientTemplate::class);
    }


    public function setPasswordAttribute($password)
    {
        $this->attributes['password'] = bcrypt($password);
    }
}