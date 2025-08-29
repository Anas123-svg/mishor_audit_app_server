<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FieldOption extends Model
{
    use HasFactory;

    protected $fillable = ['field_id', 'option_text'];

    public function field()
    {
        return $this->belongsTo(Field::class);
    }
}
