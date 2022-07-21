<?php

namespace App\Models;

use Exception;
use App\Traits\LcdtLog;
use App\Models\InvoiceState;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Invoice extends Model
{
    use HasFactory;
    use LcdtLog;
    use SoftDeletes;


    public function state() 
    {
        return $this->belongsTo(InvoiceState::class, 'invoice_state_id');
    }


    
    public function updateState($invoice_state_id, $user_id=null){
        if($user_id==null)
            $user_id=Auth::user()->id;
        $user=User::find($user_id);
        if($user->affiliate->id!=$this->affiliate_id)
        throw new Exception('Invoice is not affiliated to user.');

        $invoiceState=InvoiceState::find($invoice_state_id);
        if($invoiceState==null)
        throw new Exception('Invalid invoice state.');

        $previous_invoice_state_id=$this->invoice_state_id;
        if($this->invoice_state_id!=$invoice_state_id){
            $this->invoice_state_id=$invoice_state_id;
      
            $invoiceHistory=new InvoiceHistory();
            $invoiceHistory->invoice_state_id=$invoice_state_id;
            $invoiceHistory->user_id=$user_id;
            $invoiceHistory->invoice_id=$this->id;
            $this->save();
            $invoiceHistory->save();
            $this->l('INVOICE STATE UPDATED','Invoice #'.$this->id.': status changed '.($previous_invoice_state_id==null?'':'from '.$previous_invoice_state_id).' to '.$invoice_state_id,$user_id);
        }
    }

    public function orderInvoice(){
        return $this->belongsTo(OrderInvoice::class);
    }


}
