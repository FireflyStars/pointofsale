<?php

namespace App\Models;

use App\Models\Product;
use App\Models\OuvrageDetailAffiliate;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OuvrageDetail extends Model
{
	use SoftDeletes;

    protected $table = 'ouvrage_detail';
	protected $dates = ['deleted_at'];
	protected $appends = ['ouvrage_detail_qty', 'ouvrage_detail_numberh'];

	public function OuvrageAffiliate() 
	{
		return $this->hasOne(OuvrageDetailAffiliate::class, 'ouvrage_detail_id')
		->where('affiliate_id', request()->user()->affiliate_id);	
	}

	public function getOuvrageDetailQtyAttribute() 
	{
		if(optional($this->product)->type == 'MO') return $this->ouvrage_detail_numberh;
		return !optional($this->OuvrageAffiliate)->qty ? $this->qty : $this->OuvrageAffiliate->qty;
	}

	public function getOuvrageDetailNumberhAttribute() 
	{
		return !optional($this->OuvrageAffiliate)->numberh ? $this->numberh : $this->OuvrageAffiliate->numberh;
	}

	public function product() 
	{
		return $this->belongsTo(Product::class);
	}

}