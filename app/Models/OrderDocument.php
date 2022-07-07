<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderDocument extends Model
{
    use HasFactory;
    use SoftDeletes;

    public function ged(){
        return $this->belongsTo(Ged::class);
    }
}
