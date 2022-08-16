<?php

namespace App\Models;

use App\Models\SupplierType;
use App\Models\SupplierOrder;
use App\Models\SupplierStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Supplier extends Model
{
    use HasFactory;
	use SoftDeletes;
	protected $dates = ['deleted_at'];

	public function type() 
	{
		return $this->belongsTo(SupplierType::class, 'supplier_type_id'); 
	}

	public function status() 
	{
		return $this->belongsTo(SupplierStatus::class, 'supplier_status_id');
	}

	public function orders() 
	{
		return $this->hasMany(SupplierOrder::class, 'supplier_id');
	}

}
