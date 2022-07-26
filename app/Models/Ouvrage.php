<?php

namespace App\Models;

use App\Models\Unit;
use App\Models\OuvrageTask;
use App\Models\OuvrageToit;
use App\Models\OuvrageMetier;
use App\Models\OuvrageAffiliate;
use App\Models\OuvragePrestation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Ouvrage extends Model
{
	use HasFactory;
    protected $table = 'ouvrages';
	//
	use SoftDeletes;
	protected $dates = ['deleted_at'];

	public function OuvrageAffiliate() 
	{
		return $this->hasOne(OuvrageAffiliate::class, 'ouvrages_id');
	}

	public function unit() 
	{
		return $this->belongsTo(Unit::class);
	}

	public function prestation() 
	{
		return $this->belongsTo(OuvragePrestation::class, 'ouvrage_prestation_id');
	}

	public function metier() 
	{
		return $this->belongsTo(OuvrageMetier::class, 'ouvrage_metier_id');
	}

	public function toit() 
	{
		return $this->belongsTo(OuvrageToit::class, 'ouvrage_toit_id');
	}

	public function tasks() 
	{
		return $this->hasMany(OuvrageTask::class, 'ouvrage_id');
	}

}
