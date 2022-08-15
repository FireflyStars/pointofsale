<?php

namespace App\Models;

use App\Models\SupplierStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SupplierOrder extends Model
{
    use HasFactory;
    protected $table = 'supplier_orders';

    public function status() 
    {
        return $this->belongsTo(SupplierStatus::class, 'supplier_order_state_id');
    }
}
