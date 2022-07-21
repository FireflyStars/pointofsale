<?php

namespace App\Models;

use App\Models\Unit;
use App\Models\ProductDocument;
use App\Models\ProductAffiliate;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;
	use SoftDeletes;
	protected $dates = ['deleted_at'];


	public function unit() 
	{
		return $this->belongsTo(Unit::class);
	}

	public function productAffiliate() 
	{
		return $this->hasMany(ProductAffiliate::class);
	}

	public function documents() 
	{
		return $this->hasMany(ProductDocument::class);
	}

}
