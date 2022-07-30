<?php

namespace App\Models;

use Exception;
use App\Models\Invoice;
use App\Traits\LcdtLog;
use App\Models\Customer;
use App\Models\PaiementType;
use App\Models\PaiementState;
use App\Models\PaiementHistory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Paiement extends Model
{
    use HasFactory;
    use SoftDeletes;
    use LcdtLog;

    protected $guarded = ['id'];


    public function type() 
    {
        return $this->belongsTo(PaiementType::class, 'paiement_type_id');
    }

    public function state() 
    {
        return $this->belongsTo(PaiementState::class, 'paiement_state_id');
    }

    public function customer() 
    {
        return $this->belongsTo(Customer::class);
    }

    public function user() 
    {
        return $this->belongsTo(User::class, 'responsable_id');
    }

    public function invoice() 
    {
        return $this->belongsTo(Invoice::class, 'invoice_id');
    }

    public function history() 
    {
        return $this->hasMany(PaiementHistory::class, 'paiement_id');
    }


    public function updateState($paiement_state_id, $user_id=null){
        if($user_id==null)
            $user_id=Auth::user()->id;
        $user=User::find($user_id);
        if($user->affiliate->id!=$this->affiliate_id)
        throw new Exception('Invoice is not affiliated to user.');

        $paiementState=PaiementState::find($paiement_state_id);
        if($paiementState==null)
        throw new Exception('Invalid paiement state.');

        $previous_paiement_state_id=$this->paiement_state_id;
        if($this->paiement_state_id!=$paiement_state_id){
            $this->paiement_state_id=$paiement_state_id;
      
            $paiementHistory=new PaiementHistory();
            $paiementHistory->paiement_state_id=$paiement_state_id;
            $paiementHistory->user_id=$user_id;
            $paiementHistory->paiement_id=$this->id;
            $this->save();
            $paiementHistory->save();
            $this->l('PAIEMENT STATE UPDATED','Paiment #'.$this->id.': status changed '.($previous_paiement_state_id==null?'':'from '.$previous_paiement_state_id).' to '.$paiement_state_id,$user_id);
        }
    }
}
