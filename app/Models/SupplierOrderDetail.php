<?php

namespace App\Models;

use App\Models\Unit;
use App\Models\Product;
use App\Models\SupplierOrder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SupplierOrderDetail extends Model
{
    
    use HasFactory;

    protected $table = 'supplier_order_detail';
    protected $guarded = ['id'];

    public function product() 
    {
        return $this->belongsTo(Product::class, 'id_product');
    }

    public function order() 
    {
        return $this->belongsTo(SupplierOrder::class, 'id_order');
    }

    public function unit() 
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }

}
