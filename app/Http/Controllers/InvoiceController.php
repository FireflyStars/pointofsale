<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\InvoiceState;
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
        });
        $list=$list->where('invoices.affiliate_id','=',$user->affiliate->id);
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
}
