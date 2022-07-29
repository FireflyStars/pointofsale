<?php

namespace App\Models;

use App\Models\Unit;
use App\Models\OuvrageDetail;
use App\Models\OuvrageTaskAffiliate;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OuvrageTask extends Model
{
	use HasFactory;
	use SoftDeletes;

    protected $table = 'ouvrage_task';
	protected $dates = ['deleted_at'];
	protected $appends = ['affiliated_textcustomer'];


	public function unit() 
	{
		return $this->belongsTo(Unit::class);
	}

	public function OuvrageAffiliate() 
	{
		return $this->hasOne(OuvrageTaskAffiliate::class, 'ouvrage_task_id')
		->where('affiliate_id', request()->user()->affiliate_id);
	}

	public function details() 
	{
		return $this->hasMany(OuvrageDetail::class, 'ouvrage_task_id');
	}

	public function getAffiliatedTextcustomerAttribute() 
	{
		$textcustomer = is_null(optional($this->OuvrageAffiliate)->textcustomer) 
		? $this->textcustomer 
		: $this->OuvrageAffiliate->textcustomer;

		return $textcustomer ?? '--/--';

	}

}