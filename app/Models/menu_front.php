<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class menu_front extends Model
{
    use HasFactory;

    protected $table = 'menu_front';

    public function children() 
    {
        return $this->hasMany(static::class, 'parent_id');
    }


}
