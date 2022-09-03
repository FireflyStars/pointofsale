<?php

namespace App\Models;

use Exception;
use App\Traits\LcdtLog;
use App\Models\Customer;
use App\Models\InvoiceState;
use App\Models\InvoiceDetail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Invoice extends Model
{
    use HasFactory;
    use LcdtLog;
    use SoftDeletes;

    protected $guarded = ['id'];


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
            if($invoice_state_id==6){//valide
                   $this->reference=InvoiceNumber::getInvoiceNumber($this->id);
                    if($this->order_invoice_id>0){
                        $orderInvoice=OrderInvoice::find($this->order_invoice_id);
                        $orderInvoice->facturer=1;
                        $orderInvoice->save();    
                    }
            }
            $this->save();
            $invoiceHistory->save();
            $this->l('INVOICE STATE UPDATED','Invoice #'.$this->id.': status changed '.($previous_invoice_state_id==null?'':'from '.$previous_invoice_state_id).' to '.$invoice_state_id,$user_id);
        }
    }

    public function orderInvoice(){
        return $this->belongsTo(OrderInvoice::class);
    }
    public function invoiceType(){
        return $this->belongsTo(InvoiceType::class);
    }

    public function customer() 
    {
        return $this->belongsTo(Customer::class);
    }

    public function details() 
    {
        return $this->hasMany(InvoiceDetail::class, 'invoice_id');
    }

    public function order() 
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public static function  totalPaid($invoice_id){
       $total= DB::table('paiements')->select(DB::raw('sum(montantpaiement) as total'))->where('invoice_id','=',$invoice_id)->where('paiement_state_id','=',2)->value('total');
        if($total==null)
        $total=0;
        return $total;
    }

    public static function getModeDePaiementsByOrderId($order_id){
        $modedepaiements= DB::table('invoices')->select(DB::raw('DISTINCT(paiement_types.name) as mode_de_paiement'))->whereNull('invoices.deleted_at')->where('order_id','=',$order_id)
        ->join('paiements',function($join){
            $join->on('paiements.invoice_id','=','invoices.id')
            ->whereNull('paiements.deleted_at');
        })
        ->join('paiement_types','paiement_types.id','=','paiements.paiement_type_id')->get();
        $mode_paiements=[];
        foreach($modedepaiements as $p){
            $mode_paiements[]=$p->mode_de_paiement;
        }
        if(empty($mode_paiements)){
            $mode_paiements[]='Pas de paiement';
        }
        return $mode_paiements;
    }

    public static function getModeDePaiementsByInvoiceId($invoice_id){
        $modedepaiements= DB::table('paiements')->select(DB::raw('DISTINCT(paiement_types.name) as mode_de_paiement'))->whereNull('paiements.deleted_at')->where('invoice_id','=',$invoice_id)
        ->join('paiement_types','paiement_types.id','=','paiements.paiement_type_id')->get();
        $mode_paiements=[];
        foreach($modedepaiements as $p){
            $mode_paiements[]=$p->mode_de_paiement;
        }
        if(empty($mode_paiements)){
            $mode_paiements[]='Pas de paiement';
        }
        return $mode_paiements;
    }
}
