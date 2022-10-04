<?php

namespace App\Models;

use App\Models\Tax;
use App\Models\User;
use App\Models\Supplier;
use App\Models\SupplierStatus;
use App\Models\SupplierOrderState;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SupplierOrder extends Model
{
    
    use HasFactory;

    protected $table = 'supplier_orders';
    protected $guarded = ['id'];

    public function status() 
    {
        return $this->belongsTo(SupplierStatus::class, 'supplier_order_state_id');
    }

    public function supplier() 
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    public function user() 
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function state() 
    {
        return $this->belongsTo(SupplierOrderState::class, 'supplier_order_state_id');
    }

    public function tax() 
    {
        return $this->belongsTo(Tax::class, 'taxe_id');
    }

    public function details() 
    {
        return $this->hasMany(SupplierOrderDetail::class, 'id_order');
    }


}
