<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OuvrageAffiliate extends Model
{
    use HasFactory;
    protected $table = 'ouvrages_affiliate';
    protected $guarded = ['id'];
}
