<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OuvrageTaskAffiliate extends Model
{
    use HasFactory;
    protected $table = 'ouvrage_task_affiliates';
    protected $guarded = ['id'];
}
