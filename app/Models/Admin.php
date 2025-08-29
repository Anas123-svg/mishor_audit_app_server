<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens; 

class Admin extends Authenticatable
{
    use Notifiable;
    use HasApiTokens;
    use HasFactory;

    protected $fillable = [
        'name', 'email', 'role', 'profile_image', 'password'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    public function setPasswordAttribute($password)
    {
        $this->attributes['password'] = bcrypt($password);
    }
}
