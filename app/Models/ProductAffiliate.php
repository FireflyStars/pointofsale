<?php

namespace App\Models;

use Illuminate\Database\Query\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductAffiliate extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'product_affiliate';

    protected $guarded = ['id'];
   

}
