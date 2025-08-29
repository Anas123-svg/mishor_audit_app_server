<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomTable extends Model
{
    use HasFactory;

    protected $fillable = ['template_id', 'table_name', 'table_data'];

    public function template()
    {
        return $this->belongsTo(Template::class);
    }
}
