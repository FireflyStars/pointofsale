<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OuvrageDetailAffiliate extends Model
{
    protected $table = 'ouvrage_detail_affiliate';
	//
	use SoftDeletes;
	protected $dates = ['deleted_at'];
}