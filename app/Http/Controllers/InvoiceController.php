<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\Invoice;
use App\Models\InvoiceState;
use App\Models\InvoiceType;
use App\Models\Order;
use App\Models\OrderInvoice;
use App\Models\OrderState;
use App\Models\Paiement;
use App\Models\PaiementState;
use App\Models\PaiementType;
use App\Traits\LcdtLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use stdClass;


class InvoiceController extends Controller
{
    //

    public function loadInvoiceList(Request $request){

        $column_filters=$request->post('column_filters');
        $column_sortby=$request->post('sortby');
        $skip=$request->post('skip');
        $take=$request->post('take');
        $user=Auth::user();

        $list=DB::table('invoices')
        ->select(['invoices.*',
        'events.id as evnt',
        'users.name as responsable', 
        'invoice_types.sign',
        DB::raw("DATE_FORMAT(invoices.dateecheance, '%Y-%m-%d') as dateecheance"),
        DB::raw("0 as payer"),
        DB::raw("CONCAT(customers.company,' ',customers.firstname,' ',customers.name) as customer"),
        DB::raw("TRIM(CONCAT(contacts.firstname,' ',contacts.name,'<br/>',contacts.mobile)) as contact"),
        DB::raw("TRIM(CONCAT(addresses.address1, IF(addresses.address2<>'', '<br/>',''),addresses.address2,'<br/>',addresses.postcode,' ',addresses.city)) as address")])
          ->join('customers',function($join){
            $join->on('customers.id','=','invoices.customer_id');
        })->leftJoin('orders',function($join){
            $join->on('invoices.order_id','=','orders.id');
        })->leftJoin('events',function($join){
            $join->on('events.order_id','=','orders.id');
        })->leftJoin('contacts',function($join){
            $join->on('contacts.id','=','events.contact_id');
        })->leftJoin('addresses',function($join){
            $join->on('addresses.id','=','events.address_id')
            ->where('addresses.address_type_id','=',2);
        })->leftJoin('users',function($join){
            $join->on('users.id','=','orders.responsable_id');
        })
        ->leftJoin('invoice_types',function($join){
            $join->on('invoice_types.id','=','invoices.invoice_type_id');
        });
        $list=$list->where('invoices.affiliate_id','=',$user->affiliate->id)->where('invoices.invoice_type_id','<>',2);
        $list=$list->whereNull('invoices.deleted_at');
        //column filters
        if($column_filters!=null)
        foreach($column_filters as $column_filter){
       
            if($column_filter['type']=='date'){
                if(isset($column_filter['having'])&&$column_filter['having']==true){
                    if($column_filter['word']['from']!=''){
                        $list=$list->having($column_filter['id'],'>=',$column_filter['word']['from']);
                    }
                    if($column_filter['word']['to']!=''){
                        $list=$list->having($column_filter['id'],'<',$column_filter['word']['to']);
                    }
                }else{
                   if($column_filter['word']['from']!=''){
                        $list=$list->whereDate((isset($column_filter['table'])?$column_filter['table'].'.':'').$column_filter['id'],'>=',$column_filter['word']['from']);
                    }
                    if($column_filter['word']['to']!=''){
                        $list=$list->whereDate((isset($column_filter['table'])?$column_filter['table'].'.':'').$column_filter['id'],'<',$column_filter['word']['to']);
                    }
                }
            }elseif(isset($column_filter['filter_options'])&&count($column_filter['word'])>0){
           
                if(isset($column_filter['having'])&&$column_filter['having']==true){
                   
                    $list=$list->havingRaw($column_filter['id']." IN ('".implode("','",$column_filter['word'])."')");
                }else{
                    $list=$list->whereIn($column_filter['id'],$column_filter['word']);
                }
            
            }else{
                if(isset($column_filter['having'])&&$column_filter['having']==true){
                    $list=$list->having($column_filter['id'],'LIKE','%'.$column_filter['word'].'%');
                }else{
                    $list=$list->where((isset($column_filter['table'])?$column_filter['table'].'.':'').$column_filter['id'],'LIKE','%'.$column_filter['word'].'%');
                }
            }
        }

        //sortby
        if($column_sortby!=null){
        foreach($column_sortby as $sortby){
            $list=$list->orderBy($sortby['id'],$sortby['orderby']);
        }
        }else{//by default newest first
            $list=$list->orderBy('invoices.id','desc');
        }

        $list=$list->groupBy('invoices.id')->skip($skip)->take($take)->get();
        foreach($list as &$l){
            $l->payer=Invoice::totalPaid($l->id);
            $l->montant*=$l->sign;
        }
        return response()->json($list);
    }


    public function getInvoiceStatesFormatted(Request $request){
        $state=InvoiceState::all();
        $formatted_states=[];
        foreach($state as $od){
            $s=new stdClass();
            $s->id=$od->id;
            $s->value=$od->name;
            $formatted_states[]=$s;
        }
        return response()->json($formatted_states);
    }

    public function getInvoiceStates(Request $request){
        return response()->json(InvoiceState::all());
    }

    public function getInvoiceDetail(Request $request){
        $invoice_id=$request->post('invoice_id');
        $invoice=Invoice::find($invoice_id);
        $order_invoice=OrderInvoice::find($invoice->order_invoice_id);
        $invoice_type=InvoiceType::find($invoice->invoice_type_id);
        $invoice->order=null;
        if($invoice->order_invoice_id>0&&$order_invoice!=null){
        $invoice->orderInvoice;
        $invoice->invoiceTypes=InvoiceType::all();
        $invoice->orderInvoice->invoice_type_name=$invoice_type->name;
        $invoice->mode_paiements=Invoice::getModeDePaiementsByInvoiceId($invoice->id);
        $order=Order::find($order_invoice->order_id);
        $lastevent=$order->events()->orderBy('id','desc')->first();
        $order->contact=null;
        $chantier_address=null;
        if($lastevent!=null){
        $order->contact=$lastevent->contact;
        $chantier_address=$lastevent->address()->where('address_type_id','=',2)->first();
        }
        $order->formatted_chantier_address=$chantier_address==null?'Pas d\'adresse de chantier':$chantier_address->getformattedAddress();
        $order->customer;
     
        $facturation_address=Address::getFacturationAddress($order->customer->id);
        $order->formatted_facturation_address=$facturation_address==null?'Pas d\'adresse de facturation':$facturation_address->getformattedAddress();
        $invoice->order=$order;
        }else{
           $invoice->order=new \stdClass;
           $invoice->order->formatted_chantier_address ='Pas d\'adresse de chantier';
           $invoice->contact=null;
           $facturation_address=Address::getFacturationAddress($invoice->customer_id);
           $invoice->order->formatted_facturation_address=$facturation_address==null?'Pas d\'adresse de facturation':$facturation_address->getformattedAddress();

        }
      
        return response()->json($invoice);
    }

    public function updateInvoiceState(Request $request){
        $invoice_id=$request->post('invoice_id');
        $invoice_state_id=$request->post('invoice_state_id');
        $user=Auth::user();
        $invoice_state=InvoiceState::find($invoice_state_id);
        if($invoice_state==null)
        return response('Invalid invoice state',509);

        $invoice=Invoice::find($invoice_id);
        if($invoice==null){
            return response('Invoice not found',509);
        }

        
        if($user->affiliate_id!=$invoice->affiliate_id)
        return response('Cannot update invoice state. Invoice is not linked to the same affiliate as the user',509);

        $invoice->updateState($invoice_state_id);

        $invoice->fresh();
        return response()->json(['update state'=>'ok','reference'=>$invoice->reference]);

    }
    public function getInvoicePayments(Request $request){
        $invoice_id=$request->post('invoice_id');
        $invoice=Invoice::find($invoice_id);
        $user=Auth::user();
        if($invoice==null){
            return response('Cannot load payments.Invoice not found',509);
        }

        if($user->affiliate_id!=$invoice->affiliate_id)
        return response('Cannot load payments. Invoice is not linked to the same affiliate as the user',509);

        $paiements=Paiement::where('invoice_id','=',$invoice_id)->where('affiliate_id','=',$user->affiliate_id)->whereNull('deleted_at')->get();

        return response()->json(['paiements'=>$paiements,'paiement_states'=>PaiementState::all(),'paiement_types'=>PaiementType::all()]);

    }

    public function removeInvoicePayment(Request $request){
        $paiement_id=$request->post('paiement_id');

        $paiement=Paiement::find($paiement_id);
        if($paiement==null)
        return response('Cannot find payment',509);

        $user=Auth::user();
        if($paiement->affiliate_id!=$user->affiliate_id)
        return response('Impossible de supprimer le paiement. Le paiement n\'est pas lié au même affilié que l\'utilisateur',509);
        if($paiement->paiement_state_id!=1)
        return response('Impossible de supprimer le paiement. Le paiement n\'est pas en statut nouveau.',509);

        $paiement->delete();
        return response()->json(['delete'=>'ok']);
    }

    public function addInvoicePayment(Request $request){
        $payment=$request->post('paiement');


        $invoice=Invoice::find($payment['invoice_id']);
        if($invoice==null)
        return response('Impossible de trouver la facture.',509);

        $user=Auth::user();

        if($user->affiliate_id!=$invoice->affiliate_id)
        return response('Impossible d\'ajouter un paiement. La facture n\'est pas liée au même affilié que l\'utilisateur.',509);

        $p=new Paiement();

        $p->invoice_id=$invoice->id;
        $p->affiliate_id=$user->affiliate_id;
        $p->lang_id=1;
        $p->responsable_id=$user->id;
        $p->customer_id=$invoice->customer_id;
        $p->paiement_type_id=$payment['type'];
        $p->reference=$payment['reference'];
        $p->montantpaiement=$payment['montantpaiement'];
        $p->pourcentage=$payment['pourcentage'];
        $p->datepaiement=$payment['datepaiement'];
        $p->save();
        $p->fresh();
        $p->updateState(1);
        $p->mode_paiements=Invoice::getModeDePaiementsByInvoiceId($invoice->id);
        return response()->json($p);
    }
}
