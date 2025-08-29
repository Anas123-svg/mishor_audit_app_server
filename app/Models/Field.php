<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Field extends Model
{
    use HasFactory;

    protected $fillable = ['template_id', 'label', 'type', 'attributes','value','options','isFlagged'];

    protected $casts = [
        'attributes' => 'json',
        'options' => 'json',
    ];

    public function template()
    {
        return $this->belongsTo(Template::class);
    }

    public function options()
    {
        return $this->hasMany(FieldOption::class);
    }
}
