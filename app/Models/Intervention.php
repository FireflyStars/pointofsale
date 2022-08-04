<?php

namespace App\Models;

use App\Models\User;
use App\Models\Order;
use App\Models\InterventionType;
use App\Models\InterventionStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Intervention extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];

    public function status() 
    {
        return $this->belongsTo(InterventionStatus::class, 'intervention_statut_id');
    }

    public function type() 
    {
        return $this->belongsTo(InterventionType::class, 'intervention_type_id');
    }

    public function order() 
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function user() 
    {
        return $this->belongsTo(User::class);
    }

}
