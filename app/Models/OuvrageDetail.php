<?php

namespace App\Models;

use App\Models\OuvrageDetailAffiliate;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OuvrageDetail extends Model
{
	use SoftDeletes;

    protected $table = 'ouvrage_detail';
	protected $dates = ['deleted_at'];
	protected $appends = ['ouvrage_detail_qty'];

	public function OuvrageAffiliate() 
	{
		return $this->hasOne(OuvrageDetailAffiliate::class, 'ouvrage_detail_id');	
	}

	public function getOuvrageDetailQtyAttribute() 
	{
		return !$this->OuvrageAffiliate->qty ? $this->qty : $this->OuvrageAffiliate->qty;
	}

}