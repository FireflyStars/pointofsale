<?php

namespace App\Models;

use App\Models\User;
use App\Models\UserDocPermis;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserDocument extends Model
{
    use HasFactory, SoftDeletes;


    public function user() 
    {
        return $this->belongsTo(User::class);
    }

    public function userPermis() 
    {
        return $this->belongsTo(UserDocPermis::class, 'user_doc_permi_id');
    }

}
