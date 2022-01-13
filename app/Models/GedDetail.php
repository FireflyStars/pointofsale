<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GedDetail extends Model
{
    use HasFactory;

    public function gedCategory(){
        return $this->belongsTo(GedCategory::class);
    }
}