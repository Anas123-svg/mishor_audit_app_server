<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Template extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description','Activity','Reference','Assessor','Date','created_by'];    

    public function fields()
    {
        return $this->hasMany(Field::class);
    }

    public function tables() 
    {
        return $this->hasMany(CustomTable::class);
    }

    public function templates()
    {
        return $this->hasMany(ClientTemplate::class);
    }


    
}
