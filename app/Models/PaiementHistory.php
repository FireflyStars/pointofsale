<?php

namespace App\Models;

use App\Models\Paiement;
use App\Models\PaiementState;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PaiementHistory extends Model
{
    use HasFactory;

    public function state() 
    {
        return $this->belongsTo(PaiementState::class, 'paiement_state_id');
    }

    public function paiement() 
    {
        return $this->belongsTo(Paiement::class);
    }

    public function user() 
    {
        return $this->belongsTo(User::class);
    }

}
