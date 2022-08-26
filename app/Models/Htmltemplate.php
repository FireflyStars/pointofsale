<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Htmltemplate extends Model
{
    use HasFactory;
    use SoftDeletes;

    public function elements(){
        return $this->hasMany(HtmltemplateElement::class);
    }
}
