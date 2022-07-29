<?php

namespace App\Models;

use App\Models\User;
use App\Models\Order;
use App\Models\Affiliate;
use App\Models\PointageType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pointage extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'pointage';
    protected $guarded = ['id'];

    public function user() 
    {
        return $this->belongsTo(User::class);
    }

    public function type() 
    {
        return $this->belongsTo(PointageType::class, 'pointage_type_id');
    }

    public function order() 
    {
        return $this->belongsTo(Order::class);
    }

    public function affiliate() 
    {
        return $this->belongsTo(Affiliate::class);
    }

}
