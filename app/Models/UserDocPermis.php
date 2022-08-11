<?php

namespace App\Models;

use App\Models\UserDocument;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserDocPermis extends Model
{
    
    use HasFactory;
    
    protected $table = 'user_doc_permis';

    public function documents() 
    {
        return $this->hasMany(UserDocument::class, 'user_doc_permi_id');
    }


}
