<?php

namespace App\Models;

use App\Models\CustomerStatut;
use App\Models\CustomerPaiement;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Customer extends Model
{
    use HasFactory;
    use SoftDeletes;

    public function orders(){
        return $this->hasMany(Order::class);
    }

    public function addresses(){
        return $this->hasMany(Address::class);
    }

    public function contacts(){
        return $this->hasMany(Contact::class);
    }

    public function status() 
    {
        return $this->belongsTo(CustomerStatut::class, 'customer_statut_id');
    }

    public function paiement() 
    {
        return $this->belongsTo(CustomerPaiement::class, 'customer_paiement_id');
    }

}
