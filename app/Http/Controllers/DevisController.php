<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\Customer;
use App\Traits\Tools;
use App\Traits\GedFileProcessor;
use App\Models\OrderState;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use App\Models\Ged;
use App\Models\Order;
use App\Models\OrderZone;
use App\Models\GedCategory;
use App\Models\GedDetail;
use App\Models\Invoice;
use App\Models\InvoiceDetail;
use App\Models\OrderDocument;
use App\Models\OrderInvoice;
use Exception;
use stdClass;

class DevisController extends Controller
{
    use Tools;
    use GedFileProcessor;

    public function loadList(Request $request){

        $column_filters=$request->post('column_filters');
        $column_sortby=$request->post('sortby');
        $skip=$request->post('skip');
        $take=$request->post('take');
        $user=Auth::user();

        $orderList=DB::table('orders')
        ->select(['orders.*', 'events.id as evnt', 'users.name as responsable',
        DB::raw("DATE_FORMAT(orders.created_at, '%Y-%m-%d') as created_at"),
        DB::raw("CONCAT(customers.company,' ',customers.firstname,' ',customers.name) as customer"),
        DB::raw("TRIM(CONCAT(contacts.firstname,' ',contacts.name,'<br/>',contacts.mobile)) as contact"),
        DB::raw("TRIM(CONCAT(addresses.address1, IF(addresses.address2<>'', '<br/>',''),addresses.address2,'<br/>',addresses.postcode,' ',addresses.city)) as address")])
          ->join('customers',function($join){
            $join->on('customers.id','=','orders.customer_id');
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
        ->leftJoin('order_states', function($join) {
            $join->on('order_states.id', '=', 'orders.order_state_id');
        });
        $orderList=$orderList->where('orders.affiliate_id','=',$user->affiliate->id);
        $orderList=$orderList->whereNull('orders.deleted_at')->where('order_states.order_type', '=', 'DEVIS');
        //column filters
        if($column_filters!=null)
        foreach($column_filters as $column_filter){

            if($column_filter['type']=='date'){
                if(isset($column_filter['having'])&&$column_filter['having']==true){
                    if($column_filter['word']['from']!=''){
                        $orderList=$orderList->having($column_filter['id'],'>=',$column_filter['word']['from']);
                    }
                    if($column_filter['word']['to']!=''){
                        $orderList=$orderList->having($column_filter['id'],'<',$column_filter['word']['to']);
                    }
                }else{
                   if($column_filter['word']['from']!=''){
                        $orderList=$orderList->whereDate((isset($column_filter['table'])?$column_filter['table'].'.':'').$column_filter['id'],'>=',$column_filter['word']['from']);
                    }
                    if($column_filter['word']['to']!=''){
                        $orderList=$orderList->whereDate((isset($column_filter['table'])?$column_filter['table'].'.':'').$column_filter['id'],'<',$column_filter['word']['to']);
                    }
                }
            }elseif(isset($column_filter['filter_options'])&&count($column_filter['word'])>0){

                if(isset($column_filter['having'])&&$column_filter['having']==true){

                    $orderList=$orderList->havingRaw($column_filter['id']." IN ('".implode("','",$column_filter['word'])."')");
                }else{
                    $orderList=$orderList->whereIn($column_filter['id'],$column_filter['word']);
                }

            }else{
                if(isset($column_filter['having'])&&$column_filter['having']==true){
                    $orderList=$orderList->having($column_filter['id'],'LIKE','%'.$column_filter['word'].'%');
                }else{
                    $orderList=$orderList->where((isset($column_filter['table'])?$column_filter['table'].'.':'').$column_filter['id'],'LIKE','%'.$column_filter['word'].'%');
                }
            }
        }

        //sortby
        if($column_sortby!=null){
        foreach($column_sortby as $sortby){
            $orderList=$orderList->orderBy($sortby['id'],$sortby['orderby']);
        }
        }else{//by default newest first
            $orderList=$orderList->orderBy('id','desc');
        }

        $orderList=$orderList->groupBy('orders.id')->skip($skip)->take($take)->get();
        return response()->json($orderList);
    }

    public function getOrderDetail(Request $request)
    {
        $order_id=$request->post('order_id');
        $order=Order::find($order_id);
        $lastevent=$order->events()->orderBy('id','desc')->first();
        $order->contact=null;
        $chantier_address=null;
        if($lastevent!=null){
            $order->contact=$lastevent->contact;
            $chantier_address=$lastevent->address()->where('address_type_id','=',2)->first();
        }
        $order->formatted_chantier_address=$chantier_address==null?'Pas d\'adresse de chantier':$chantier_address->getformattedAddress();
        $order->customer;
        $order->orderZones;
        $order->mode_paiements=Invoice::getModeDePaiementsByOrderId($order->id);

        foreach($order->orderZones as &$orderzone)
        {
            $orderzone->orderOuvrage;
            $groupOrderOuvrage=[];
            foreach( $orderzone->orderOuvrage as $order_ouvrage){

                $order_ouvrage->orderCategory;
                $groupOrderOuvrage[$order_ouvrage->orderCategory->name][]=$order_ouvrage;
            }
            $orderzone->groupedOrderOuvrage=$groupOrderOuvrage;
        }
        $facturation_address=Address::getFacturationAddress($order->customer->id);
        $order->formatted_facturation_address=$facturation_address==null?'Pas d\'adresse de facturation':$facturation_address->getformattedAddress();
        $order->state=OrderState::find($order->order_state_id);
        return response()->json($order);
    }

    public function setOrderState(Request $request){
        $order_state_id=$request->post('order_state_id');
        $order_id=$request->post('order_id');
        $order=Order::find($order_id);
        $order->updateState($order_state_id);
    }
    public function loadOrderInvoices(Request $request){
        $order_id=$request->post('order_id');
        $user=Auth::user();
        $order=Order::find($order_id);
        if($order->affiliate_id!=$user->affiliate_id)
        return response('Order is not affiliated to user.Cannot load invoices.',509);


        $orderInvoices=DB::table('order_invoices')
        ->select(['order_invoices.*','invoice_types.sign','invoice_types.name as invoice_type_name','invoice_types.color as invoice_type_color','invoices.reference as ref','invoices.invoice_state_id'])
        ->leftJoin('invoices',function($join){
             $join->on('invoices.order_invoice_id','=','order_invoices.id')
             ->whereNull('invoices.deleted_at');
        })->leftJoin('invoice_types',function($join){
             $join->on('invoice_types.id','=','invoices.invoice_type_id')
             ->whereNull('invoice_types.deleted_at');
        })->where('order_invoices.order_id','=',$order->id)
        ->whereNull('order_invoices.deleted_at')
        ->orderBy('order_invoices.id')
        ->get();
        return response()->json($orderInvoices);

    }

    public function loadOrderDocuments(Request $request)
    {
            $order_id=$request->post('order_id');
            $order=Order::find($order_id);
            $order_documents=array();

            $user=Auth::user();
            if($order->affiliate_id!=$user->affiliate_id)
            return response('Order is not affiliated to user.Cannot load order documents.',509);

            $reports=$order->reports->makeHidden(['pages','page_files','deleted_at','updated_at','affiliate_id','user_id']);
            foreach($reports as $report){
                $carbon=Carbon::createFromFormat('Y-m-d H:i:s',$report->created_at);
                $report->formatted_date=$carbon->format('d/m/Y H:i');
                $report->user;
                $report->strtotime=strtotime($report->created_at);
                $order_documents[]=$report;
            }
            $geds=$order->geds;
           foreach($geds as $ged){
                $documents=$ged->orderDocuments;
                foreach($documents as $document){
                    $carbon=Carbon::createFromFormat('Y-m-d H:i:s',$document->created_at);
                    $document->formatted_date=$carbon->format('d/m/Y H:i');
                    $document->user=$ged->user;
                    $document->name=$document->human_readable_filename;
                    $document->strtotime=strtotime($document->created_at);
                    $order_documents[]=$document;
                }

           }


            return response()->json($order_documents);
    }

    public function uploadOrderDocument(Request $request){
        $file =$request->file('files');
        $order_id=$request->post('order_id');
        $fileName = time().'_'.$file->getClientOriginalName();
        $filePath = $file->storeAs('uploads', $fileName, 'public');
        $user=Auth::user();
        $order=Order::find($order_id);
        if($order==null)
        return response('Order not found.',509);

        if($order->affiliate_id!=$user->affiliate_id)
        return response('Order is not affiliated to user.Cannot save order document.',509);

        $ged=Ged::where('user_id','=',$user->id)->where('order_id','=',$order_id)->whereNull('deleted_at')->first();
        if($ged==null){
            $ged=new Ged();
            $ged->user_id=$user->id;
            $ged->order_id=$order_id;
            $ged->customer_id=$order->customer->id;
            $ged->save();
            $ged->fresh();
        }
        $od=new OrderDocument();
        $od->ged_id=$ged->id;
        $od->human_readable_filename=$file->getClientOriginalName();
        $uuid_filename = DB::select('select UUID() AS uuid')[0]->uuid;
        $od->name=$uuid_filename.'.'.$file->getClientOriginalExtension();
        $od->file_path=$file->storeAs('GED/GED_'.$od->ged_id.'/order_documents', $od->name, 'public');
        $od->save();
    }
    public function removeOrderDocument(Request $request){
        $order_document_id=$request->post('order_document_id');
        $od=OrderDocument::find($order_document_id);
        $order=$od->ged->order;
        $user=Auth::user();

        if($order->affiliate_id!=$user->affiliate_id)
        return response('Order is not affiliated to user.Cannot save order document.',509);

        $od->delete();
    }

    public function getOrderDocumentUrl(Request $request){
        $order_document_id=$request->post('order_document_id');
        $od=OrderDocument::find($order_document_id);
        $order=$od->ged->order;
        $user=Auth::user();

        if($order->affiliate_id!=$user->affiliate_id)
        return response('Order is not affiliated to user.Cannot download.',509);

        return response()->json(array('document_url'=>route('downloadPdfFile').'?path='.$od->file_path.'&filename='.$od->human_readable_filename));
    }
    public function newOrderInvoice(Request $request){

        $description=$request->post('description');
        $taux=$request->post('taux');
        $montant=$request->post('montant');
        $order_id=$request->post('order_id');
        $date=$request->post('date');
        $invoice_type_id=$request->post('invoice_type_id');
        $user=Auth::user();
        $order=Order::find($order_id);
        if($order->affiliate_id!=$user->affiliate_id)
        return response('Order is not affiliated to user.Cannot add invoice.',509);






        $oi=new OrderInvoice();
        $oi->description=$description;
        $oi->dateinvoice=$date;
        $oi->pourcentage=$taux;
        $oi->montant=$montant;
        $oi->order_id=$order_id;
        $oi->save();
        $oi->fresh();


            $oi->facturer=0;
            $in=new Invoice();
            $in->order_id=$order_id;
            $in->montant=$oi->montant;
            $in->pourcentage=str_replace('%','',$oi->pourcentage);
            $in->lang_id=1;
            $in->customer_id=$order->customer_id;
            $in->order_invoice_id=$oi->id;
            $in->responsable_id=$user->id;
            $in->affiliate_id=$order->affiliate_id;
            $in->invoice_type_id=$invoice_type_id;
            $in->dateecheance=$date;
            $in->save();
            $in->fresh();
            if($invoice_type_id!=2)
            $in->reference='PROV'.$in->id;
            $in->updateState(1);//creation
            $oi->invoice_id=$in->id;
            $oi->save();

        if($invoice_type_id==1||$invoice_type_id==4||$invoice_type_id==3){
            $inD=new InvoiceDetail();
            $fdate=date('d/m/Y',strtotime($date));
        if($invoice_type_id==1||$invoice_type_id==4){
            $descr="Échéance: $description prévu pour $fdate";
        }
        if($invoice_type_id==3){
            $descr="Avoir: $description prévu pour $fdate";
        }
        $inD->description=$descr;
        $inD->montant=$montant;
        $inD->invoice_id=$in->id;
        $inD->tax_id=$order->customer->taxe_id;
        $inD->save();
        }



        $orderInvoices=DB::table('order_invoices')->select(['order_invoices.*','invoice_types.sign','invoice_types.name as invoice_type_name','invoice_types.color as invoice_type_color'])->leftJoin('invoices',function($join){
            $join->on('invoices.order_invoice_id','=','order_invoices.id')
            ->whereNull('invoices.deleted_at');
       })->leftJoin('invoice_types',function($join){
            $join->on('invoice_types.id','=','invoices.invoice_type_id')
            ->whereNull('invoice_types.deleted_at');
       })->where('order_invoices.order_id','=',$order->id)
       ->whereNull('order_invoices.deleted_at')->get();

       $reste_a_facturer=0;
       $total_facture=0;
       $total_taux_facture=0;
       $total_remise=0;


       foreach($orderInvoices as $orderInvoice){
         if($orderInvoice->invoice_type_name=='FACTURE'&&$orderInvoice->facturer==1){
            $total_facture+=$orderInvoice->montant;
            $total_taux_facture+=$orderInvoice->pourcentage;
         }
         if($orderInvoice->invoice_type_name=='REMISE'){
            $total_remise+=$orderInvoice->montant;
         }
       }

       $reste_a_facturer=$order->total-$total_remise-$total_facture;

        if($invoice_type_id==2){// mettre a jour les echeance qui ne sont pas encore facturer
            foreach($orderInvoices as $orderInvoice){
                if($orderInvoice->invoice_type_name=='FACTURE'&&$orderInvoice->facturer==0){
                    $montant=($reste_a_facturer/(100-$total_taux_facture)*$orderInvoice->pourcentage);
                    $oi=OrderInvoice::find($orderInvoice->id);
                    $oi->montant=$montant;
                    $oi->save();
                }
            }
        }



    }
    public function removeOrderInvoice(Request $request){
        $order_invoice_id=$request->post('id');
        $invoice_type_name=$request->post('invoice_type_name');

        $oi=OrderInvoice::find($order_invoice_id);
        if($oi==null)
            return response('Order invoice not found.',509);



        $order=$oi->order;
        $user=Auth::user();
        if($order->affiliate_id!=$user->affiliate_id)
        return response('Order is not affiliated to user.Cannot delete invoice.',509);


        if($oi->invoice_id>0){
            $invoice=Invoice::find($oi->invoice_id);
            if($invoice->invoice_state_id!=1)
                return response('Invoice already exists.Cannot delete',509);

                if($invoice_type_name=='AVOIR'&&$invoice->invoice_state_id!=1)
                return response('Impossible de supprimer une avoir.',509);

            if(($invoice->invoice_type_id==1||$invoice->invoice_type_id==2||$invoice->invoice_type_id==3)&&$invoice->invoice_state_id==1){//if facture or remise and still in creation we can delete. we cannot delete avoir
                $invoice->delete();
        }
        }


        $oi->delete();


        if($invoice_type_name=='REMISE'){
             // mettre a jour les echeance qui ne sont pas encore facturer
             $orderInvoices=DB::table('order_invoices')->select(['order_invoices.*','invoice_types.sign','invoice_types.name as invoice_type_name','invoice_types.color as invoice_type_color'])->leftJoin('invoices',function($join){
                $join->on('invoices.order_invoice_id','=','order_invoices.id')
                ->whereNull('invoices.deleted_at');
           })->leftJoin('invoice_types',function($join){
                $join->on('invoice_types.id','=','invoices.invoice_type_id')
                ->whereNull('invoice_types.deleted_at');
           })->where('order_invoices.order_id','=',$order->id)
           ->whereNull('order_invoices.deleted_at')->get();

           $reste_a_facturer=0;
           $total_facture=0;
           $total_taux_facture=0;
           $total_remise=0;


           foreach($orderInvoices as $orderInvoice){
             if($orderInvoice->invoice_type_name=='FACTURE'&&$orderInvoice->facturer==1){
                $total_facture+=$orderInvoice->montant;
                $total_taux_facture+=$orderInvoice->pourcentage;
             }
             if($orderInvoice->invoice_type_name=='REMISE'){
                $total_remise+=$orderInvoice->montant;
             }
           }

           $reste_a_facturer=$order->total-$total_remise-$total_facture;


                foreach($orderInvoices as $orderInvoice){
                    if($orderInvoice->invoice_type_name=='FACTURE'&&$orderInvoice->facturer==0){

                        $montant=($reste_a_facturer/(100-$total_taux_facture)*$orderInvoice->pourcentage);
                        $oi=OrderInvoice::find($orderInvoice->id);
                        $oi->montant=$montant;
                        $oi->update();
                    }
                }

            }


        return response()->json(array('message'=>'ok'));
    }
    public function validateOrderInvoice(Request $request){
        $order_invoice_id=$request->post('id');
        $invoice_type_name=$request->post('invoice_type_name');

        $oi=OrderInvoice::find($order_invoice_id);
        if($oi==null)
            return response('Order invoice not found.',509);


        $order=$oi->order;
        $user=Auth::user();
        if($order->affiliate_id!=$user->affiliate_id)
        return response('Order is not affiliated to user.Cannot create invoice.',509);


        if($oi->invoice_id>0&&$oi->facturer==1)
        return response('Invoice already exists.',509);

        $oi->facturer=1;
        $in=Invoice::find($oi->invoice_id);

        $in->updateState(6);//valide
        $oi->save();
        $in->ref=$in->reference;


        return response()->json(array('message'=>'ok','invoice'=>$in));

    }
    public function getOrderStates(Request $request){
        return response()->json(OrderState::all());
    }
    public function getOrderStatesFormatted(Request $request){
        $orderstates=OrderState::all();
        $formatted_order_states=[];
        foreach($orderstates as $od){
            $s=new stdClass();
            $s->id=$od->id;
            $s->value=$od->name;
            $formatted_order_states[]=$s;
        }
        return response()->json($formatted_order_states);
    }
    /**
     * Get Ged categories
     *
     */

    public function getGedCategories(){
        $categories = DB::table('ged_categories')->select('id', 'name')->get();
        foreach ($categories as $item) {
            $item->items = [];
        }
        $describes = DB::table('describe')->join('describe_categories', 'describe.describe_category_id', '=', 'describe_categories.id')
                    ->select('describe.id', 'describe.name', 'describe.type', 'describe.default', 'describe.order', 'describe.data', 'describe_categories.name as catName')
                    ->orderBy('order')->get();
        foreach($describes as $describe){
            $describe->data = json_decode($describe->data);
            if($describe->type == 'Checkbox' || $describe->type == 'Switch'){
                $describe->default = $describe->default == 0 ? false : true;
            }
        }
        $describes = $describes->groupBy('catName');

        $services = DB::table('services')->select(
            'services.name', 'services.type', 'services.default as value',
            'services.data', 'services.id',
        )
        ->orderBy('order')->get();
        foreach($services as $service){
            $service->data = json_decode($service->data);
            if($service->type == 'Checkbox' || $service->type == 'Switch'){
                $service->value = $service->value == 0 ? false : true;
            }
            $service->active = false;
            $service->semti = false;
            $service->sstt = false;
            $service->client = false;
            $service->loc = false;
        }
        return response()->json([
            'devisWithOuvrage'   => DB::table('settings')->where('key', 'admin.ouvrage')->value('value') == 1 ? true : false,
            'useGoogleService'   => DB::table('settings')->where('key', 'admin.Google')->value('value') == 1 ? true : false,
            'gedCats'   => $categories->groupBy('id'),
            'units'     => DB::table('units')->select('id as value', 'code as display')->get(),
            'taxes'     => DB::table('taxes')->select('id as value', DB::raw('CEIL(taux * 100) as display'))->get(),
            'roofAccesses'     => DB::table('moyenacces')->select('id as value', 'name as display')->get(),
            'describes'     => $describes,
            'services'     => $services,
            'describeOn'     => DB::table('settings')->where('key', 'admin.Describe')->value('value') == 1 ? true : false,
        ]);
    }

    /**
     * Get all toits
     *
     */

    public function getAllToits(){
        return response()->json(DB::table('ouvrage_toit')->select('id', 'name', 'image')->get());
    }

    /**
     * Get all prestation ouvrages
     *
     */

    public function getPrestationOuvrages(Request $request){
        $query = DB::table('ouvrages')
                ->join('ouvrage_toit', 'ouvrage_toit.id', '=', 'ouvrages.ouvrage_toit_id')
                ->join('ouvrage_metier', 'ouvrage_metier.id', '=', 'ouvrages.ouvrage_metier_id')
                ->join('units', 'units.id', '=', 'ouvrages.unit_id')
                ->where('type', 'PRESTATION')
                ->where('ouvrage_toit_id', $request->toit == 0 ? '!=' : '=',$request->toit);

        return response()->json(
            $query->select(
                'ouvrages.id', 'ouvrages.name',
                'ouvrages.textchargeaffaire', 'ouvrage_metier.name as metier',
                'ouvrage_toit.name as toit', 'ouvrages.type', 'units.code as unit'
            )->get()
        );
    }

    public function getOuvrage(Request $request){
        $ouvrage = DB::table('ouvrages')
                    ->select(
                        'unit_id as unit', 'textcustomer as customerText', 'name',
                        'textchargeaffaire', 'textoperator', 'ouvrage_metier_id', 'ouvrage_prestation_id', 'ouvrage_toit_id'
                        )
                    ->where('id', $request->id)->first();
        $ouvrage->qty = $request->qtyOuvrage;
        $ouvrage->totalHour = 0;
        $ouvrage->avg = 0;
        $ouvrage->qtyOuvrage = $request->qtyOuvrage;
        $ouvrage->total = 0;
        $ouvrage->totalWithoutMarge = 0;
        $tasks = DB::table('ouvrage_task')
                ->where('ouvrage_id', $request->id)
                ->select('id as taskId', 'name', 'textcustomer as customerText', 'textchargeaffaire', 'textoperator', 'unit_id', 'qty')
                ->get();
        $ouvrage->tasks = $tasks;
        foreach ($tasks as $task) {
            $details = DB::table('ouvrage_detail')
                            ->join('products', 'products.id', '=', 'ouvrage_detail.product_id')
                            ->join('units', 'units.id', '=', 'products.unit_id')
                            ->leftJoin('product_affiliate', function ($join) {
                                $join->on('products.id', '=', 'product_affiliate.product_id')
                                     ->where('product_affiliate.affilie_id', '=', Auth::user()->affiliate_id);
                            })
                            ->leftJoin('ouvrage_detail_affiliate', function ($join) {
                                $join->on('ouvrage_detail.id', '=', 'ouvrage_detail_affiliate.ouvrage_detail_id')
                                     ->where('ouvrage_detail_affiliate.affiliate_id', '=', Auth::user()->affiliate_id);
                            })
                            ->where('ouvrage_task_id', $task->taskId)
                            ->select(
                                'ouvrage_detail.qty', 'units.id as unit_id','products.type', 'units.code as unit',
                                'products.id as productId', 'products.name', 'products.taxe_id as tax',
                                DB::raw('IF(ISNULL(ouvrage_detail_affiliate.numberh), ouvrage_detail.numberh, ouvrage_detail_affiliate.numberh) as numberH'),
                                DB::raw('IF(ISNULL(product_affiliate.wholesale_price), products.wholesale_price, product_affiliate.wholesale_price) as unitPrice')
                            )->get();
            foreach ($details as $detail) {
                $detail->numberH = intval($request->qtyOuvrage) * floatval($detail->numberH);
                $detail->originalNumberH = $detail->numberH;
                $detail->originalDetailQty = intval($detail->qty == 0 ? 1 : $detail->qty);
                $detail->qty = intval($detail->qty == 0 ? 1 : $detail->qty)*intval($request->qtyOuvrage);
                $detail->marge = 8;
                $detail->tax = Customer::find($request->customerId)->tax->id;
                $detail->original = true;
                $detail->unitPrice = number_format($detail->unitPrice, 2);
                $detail->qtyOuvrage = (int)$detail->qty;
                $detail->totalPriceWithoutMarge = 0;
                if($detail->type == "MO"){
                    $detail->totalPrice = ($detail->numberH * $detail->unitPrice);
                }else{
                    $detail->totalPrice = ($detail->qty * $detail->unitPrice * (1.08));
                }
                $ouvrage->totalHour += $detail->numberH;
            }
            $task->details = $details;
        }
        return response()->json($ouvrage);
    }

    /**
     * Search Ouvrages
     */
    public function searchOuvrage(Request $request){
        $query = DB::table('ouvrages')
                    ->join('ouvrage_toit', 'ouvrage_toit.id', '=', 'ouvrages.ouvrage_toit_id')
                    ->join('ouvrage_metier', 'ouvrage_metier.id', '=', 'ouvrages.ouvrage_metier_id')
                    ->join('units', 'units.id', '=', 'ouvrages.unit_id');
        if($request->search != ''){
            $query =    $query->where('ouvrages.name', 'like', '%'.$request->search.'%')
                        ->orWhere('ouvrages.codelcdt', 'like', '%'.$request->search.'%')
                        ->orWhere('ouvrages.textchargeaffaire', 'like', '%'.$request->search.'%');
        }
        if($request->type != '')
            $query =    $query->where('type', $request->type);
        return response()->json(
            $query->select(
                'ouvrages.id', 'ouvrages.name',
                'ouvrages.textchargeaffaire', 'ouvrage_metier.name as metier',
                'ouvrage_toit.name as toit', 'ouvrages.type', 'units.code as unit'
            )->get()
        );
    }

    /**
     * Search Products
     */
    public function searchProduct(Request $request){
        $query = DB::table('products')
                ->join('taxes', 'taxes.id', '=', 'products.taxe_id')
                ->join('units', 'units.id', '=', 'products.unit_id')
                ->leftJoin('product_affiliate', function ($join) {
                    $join->on('products.id', '=', 'product_affiliate.product_id')
                         ->where('product_affiliate.affilie_id', '=', Auth::user()->affiliate_id);
                });
        if($request->search != ''){
            $query =    $query->where('products.name', 'like', '%'.$request->search.'%')
                        ->orWhere('products.reference', 'like', '%'.$request->search.'%')
                        ->orWhere('products.description', 'like', '%'.$request->search.'%');
        }
        $query =    $query->where('products.type', 'PRODUIT');
        return response()->json(
            $query->select(
                'products.id', 'products.name',
                'products.description', 'products.type', 'products.unit_id', /* DB::raw('CEIL(taxes.taux * 100) as tax'), */
                'products.reference', 'products.type', 'units.code as unit', 'products.taxe_id as tax',
                DB::raw('IF(ISNULL(product_affiliate.wholesale_price), products.wholesale_price, product_affiliate.wholesale_price) as wholesale_price')
            )->get()
        );
    }

    /**
     * Get Suppliers
     */
    public function getSuppliers(){
        return response()->json(
            DB::table('suppliers')->select('id as value', 'name as display')->get()
        );
    }

    /**
     * Get info to add empty ouvrage
     */
    public function getInfoForEmtpyOuvrage(){
        return response()->json([
            'units'         =>  DB::table('units')->select('code as display', 'id as value')->get(),
            'toits'         =>  DB::table('ouvrage_toit')->select('name as display', 'id as value')->get(),
            'metiers'       =>  DB::table('ouvrage_metier')->select('name as display', 'id as value')->get(),
            'prestations'   =>  DB::table('ouvrage_prestation')->select('name as display', 'id as value')->get(),
        ]);
    }

    /**
     * Get some datas for Adding a interim Modal
     *
     */
    public function getInterimData(){
        return response()->json([
            'societes'  => DB::table('interim_societe')->select('id as value', 'name as display')->get(),
            'interim'    =>
                DB::table('products')
                ->leftJoin('product_affiliate', function ($join) {
                    $join->on('products.id', '=', 'product_affiliate.product_id')
                         ->where('product_affiliate.affilie_id', '=', Auth::user()->affiliate_id);
                })
                ->where('products.type', 'INTERIM')->select(
                    DB::raw('IF(ISNULL(product_affiliate.wholesale_price), products.wholesale_price, product_affiliate.wholesale_price) as price'),
                    'products.taxe_id as tax', 'products.id as productId', 'products.unit_id as unitId'
                )->first()
        ]);
    }
    /**
     * Get MO product's price
     *
     */
    public function getLaborData(){
        return response()->json(
            DB::table('products')
            ->leftJoin('product_affiliate', function ($join) {
                $join->on('products.id', '=', 'product_affiliate.product_id')
                     ->where('product_affiliate.affilie_id', '=', Auth::user()->affiliate_id);
            })
            ->where('products.type', 'MO')->select(
                DB::raw('IF(ISNULL(product_affiliate.wholesale_price), products.wholesale_price, product_affiliate.wholesale_price) as price'),
                'products.taxe_id as tax', 'products.id as productId', 'products.unit_id as unitId'
            )->first()
        );
    }
    /**
     * Get units
     */
    public function getUnits(){
        return response()->json([
            DB::table('units')->select('code as display', 'id as value')->get()
        ]);
    }
    /**
     * Save a new devis
     */
    public function storeDevis(Request $request){
        if(DB::table('settings')->where('key', 'admin.ouvrage')->value('value') == 1 ){
            $orderData = [
                'lang_id'           => 1,
                'name'              => $request->orderName,
                'affiliate_id'      => Auth::user()->affiliate->id,
                'responsable_id'    => Auth::id(),
                'order_state_id'    => 2,
                'total'             => ($request->totalPriceForInstall + $request->totalPriceForSecurity + $request->totalPriceForPrestation),
                'nbheure'           => ($request->totalHoursForInstall + $request->totalHoursForSecurity + $request->totalHoursForPrestation),
                'address_id'        => $request->address['id'],
                'customer_id'       => $request->customer['id'],
                'contact_id'        => $request->customer['contact_id'],
                'remise'            => $request->discount,
                'datecommande'      => Carbon::now(),
                'signed_by_customer'=> 0,
                'reference'         => $this->passwdGen(10,'NO_NUMERIC'),
                'created_at'        => Carbon::now(),
                'updated_at'        => Carbon::now(),
            ];


            $orderId = DB::table('orders')->insertGetId($orderData);
            $orderHistory = [
                'user_id'       => Auth::id(),
                'order_id'      => $orderId,
                'order_state_id'=> 2,
                'created_at'    => Carbon::now(),
                'updated_at'    => Carbon::now(),
            ];
            DB::table('order_histories')->insert($orderHistory);
            if(
                ! Ged::where('user_id','=',Auth::id())
                    ->where('customer_id', $request->customer['id'])
                    ->where('order_id', $orderId)->exists()
            ){
                $zedData = [
                    'customer_id'   =>  $request->customer['id'],
                    'user_id'       =>  Auth::id(),
                    'order_id'      =>  $orderId,
                    'created_at'    =>  Carbon::now(),
                    'updated_at'    =>  Carbon::now(),
                ];
                $gedId = DB::table('geds')->insertGetId($zedData);
            }else{
                $gedId = Ged::where('user_id','=',Auth::id())
                            ->where('customer_id', $request->customer['id'])
                            ->where('order_id', $orderId)->value('id');
            }
            foreach ($request->zones as $zone) {
                $zoneData = [
                    'order_id'      =>  $orderId,
                    'latitude'      =>  $request->address['lat'],
                    'longitude'     =>  $request->address['lon'],
                    // 'latitude'      =>  '10.197948495703532',
                    // 'longitude'     =>  '10.197948495703532',
                    'description'   =>  '',
                    'hauteur'       =>  $zone['height'],
                    'moyenacces_id' =>  $zone['roofAccess'],
                    'name'          =>  $zone['name'],
                    'created_at'    =>  Carbon::now(),
                    'updated_at'    =>  Carbon::now(),
                ];
                $zoneId = DB::table('order_zones')->insertGetId($zoneData);
                // save ged details
                foreach ($zone['gedCats'] as $gedCatId=> $gedCat) {
                    foreach ($gedCat[0]['items'] as $file) {
                        $gedDetail = new GedDetail();
                        $gedDetail->ged_id = $gedId;
                        $gedDetail->order_zone_id = $zoneId;
                        $gedDetail->ged_category_id = $gedCatId;
                        $gedDetail->user_id = Auth::id();
                        $gedDetail->save();
                        $gedDetail = $gedDetail->fresh();//retreve fresh object with all fields

                        $file['base64data'];
                        if( $gedDetail->file == null){//files can only be stored once to avoid duplicates;
                            if( !empty($file['base64data']) ){
                                $storedFile = $this->storeFile($file['base64data'], $file['fileName'], $gedDetail->id);
                                $gedDetail->file = $storedFile->file;
                                $gedDetail->type = $storedFile->type;
                                $gedDetail->storage_path = $storedFile->storage_path;
                                $gedDetail->human_readable_filename = $storedFile->human_readable_filename;
                            }
                        }
                        $gedDetail->save();
                    }
                }
                // save describe
                foreach($zone['describes'] as $zoneDescribes) {
                    $describeData = [];
                    foreach ($zoneDescribes as $describe) {
                        $describeData[] = [
                            'order_zone_id'=> $zoneId,
                            'describe_id'=> $describe['id'],
                            'value'=> $describe['default'],
                            'created_at'=> now(),
                            'updated_at'=> now()
                        ];
                    }
                    DB::table('order_describe')->insert($describeData);
                }
                // save services
                foreach($zone['services'] as $service) {
                    DB::table('order_services')->insert([
                        'order_zone_id'=> $zoneId,
                        'service_id'=> $service['id'],
                        'value'=> $service['value'],
                        'active'=> $service['active'],
                        'semti'=> $service['semti'],
                        'sstt'=> $service['sstt'],
                        'loc'=> $service['loc'],
                        'client'=> $service['client'],
                        'created_at'=> now(),
                        'updated_at'=> now()
                    ]);
                }
                if( count($zone['installOuvrage']['ouvrages']) ){
                    $installationCat = [
                        'order_zone_id' =>  $zoneId,
                        'type'          =>  'INSTALLATION',
                        'name'          =>  $zone['installOuvrage']['name'],
                        'textcustomer'  =>  '',
                        'textoperator'  =>  '',
                        'created_at'    =>  Carbon::now(),
                        'updated_at'    =>  Carbon::now()
                    ];
                    $orderCatId = DB::table('order_cat')->insertGetId($installationCat);
                    foreach ($zone['installOuvrage']['ouvrages'] as $ouvrage) {

                        $ouvrageData = [
                            'order_id'          => $orderId,
                            'order_zone_id'     => $zoneId,
                            'unit_id'           => $ouvrage['unit'],
                            'qty'               => $ouvrage['qty'],
                            'order_cat_id'      => $orderCatId,
                            'nbheure'           => $ouvrage['totalHour'],
                            'total'             => $ouvrage['total'],
                            'ouvrage_prestation_id'=> $ouvrage['ouvrage_prestation_id'],
                            'ouvrage_toit_id'   => $ouvrage['ouvrage_toit_id'],
                            'ouvrage_metier_id' => $ouvrage['ouvrage_metier_id'],
                            'textcustomer'      => $ouvrage['customerText'],
                            'textchargeaffaire' => $ouvrage['textchargeaffaire'],
                            'textoperator'      => $ouvrage['textoperator'],
                            'type'              => 'INSTALLATION',
                            'name'              => $ouvrage['name'],
                            'created_at'        => Carbon::now(),
                            'updated_at'        => Carbon::now(),
                        ];
                        $orderOuvrageId = DB::table('order_ouvrages')->insertGetId($ouvrageData);
                        foreach ($ouvrage['tasks'] as $taskIndex => $task) {
                            $orderOuvrageTask = [
                                'order_ouvrage_id'      => $orderOuvrageId,
                                'name'                  => $task['name'],
                                'textcustomer'          => $task['customerText'],
                                'textchargeaffaire'     => $task['textchargeaffaire'],
                                'textoperator'          => $task['textoperator'],
                                'qty'                   => $task['qty'],
                                'unit_id'               => $task['unit_id'],
                                'created_at'            => Carbon::now(),
                                'updated_at'            => Carbon::now(),
                            ];
                            $orderOuvrageTaskId = DB::table('order_ouvrage_task')->insertGetId($orderOuvrageTask);
                            foreach ($task['details'] as $detailIndex => $detail) {
                                $detailData = [
                                    'order_ouvrage_task_id'   => $orderOuvrageTaskId,
                                    'product_id'        => $detail['productId'],
                                    'type'              => $detail['type'],
                                    'name'              => $detail['name'],
                                    'textcustomer'      => '',
                                    'textchargeaffaire' => '',
                                    'textoperator'      => '',
                                    'qty'               => $detail['qty'],
                                    'numberh'           => $detail['numberH'],
                                    'unitprice'         => $detail['unitPrice'],
                                    'qtyouvrage'        => $detail['qtyOuvrage'] == '' ? 0 : $detail['qtyOuvrage'],
                                    'qtyunitouvrage'    => 1,
                                    'houvrage'          => 0,
                                    'marge'             => $detail['marge'],
                                    'totalprice'        => $detail['totalPrice'],
                                    'taxe_id'           => $detail['tax'],
                                    'unit_id'           => $detail['unit_id'],
                                    'priceachat'        => $detail['unitPrice'],
                                    'original'          => $detail['original'] ?? false,
                                    'original_number_h' => $detail['originalNumberH'] ?? 1,
                                    'original_qty'      => $detail['originalDetailQty'] ?? 1,
                                    'created_at'        => Carbon::now(),
                                    'updated_at'        => Carbon::now(),
                                ];
                                if($detail['type'] == 'INTERIM'){
                                    $detailData['interim_societe_id']   = $detail['societe'];
                                }
                                if($detail['type'] == 'COMMANDE FOURNISSEUR'){
                                    $detailData['supplier_id']   = $detail['supplierId'];
                                    $detailData['datesupplier']   = $detail['datesupplier'];
                                    if(preg_match('/^data:application\/pdf;base64,/', $detail['base64'], $type)){
                                        $data = substr($detail['base64'], strpos($detail['base64'], ',') + 1);
                                        $type = 'pdf';
                                        if(!File::isDirectory(public_path('SupplierOrder/'))){
                                            File::makeDirectory(public_path('SupplierOrder/'), 0755, true, true);
                                        }
                                        $data = str_replace( ' ', '+', $data );
                                        $data = base64_decode($data);
                                        $uuid_filename = DB::select('select UUID() AS uuid')[0]->uuid;
                                        $orderFilePath = "/SupplierOrder/{$uuid_filename}.{$type}";
                                        Storage::disk('public')->put($orderFilePath, $data);
                                        $detailData['orderfile'] = $orderFilePath;
                                    }
                                }
                                DB::table('order_ouvrage_detail')->insert($detailData);
                            }
                        }
                    }
                }
                if( count($zone['securityOuvrage']['ouvrages']) ){
                    $securityCat = [
                        'order_zone_id' =>  $zoneId,
                        'type'          =>  'SECURITE',
                        'name'          =>  $zone['securityOuvrage']['name'],
                        'textcustomer'  =>  '',
                        'textoperator'  =>  '',
                        'created_at'    =>  Carbon::now(),
                        'updated_at'    =>  Carbon::now()
                    ];
                    $orderCatId = DB::table('order_cat')->insertGetId($securityCat);
                    foreach ($zone['securityOuvrage']['ouvrages'] as $ouvrage) {
                        $ouvrageData = [
                            'order_id'          => $orderId,
                            'order_zone_id'     => $zoneId,
                            'unit_id'           => $ouvrage['unit'],
                            'qty'               => $ouvrage['qty'],
                            'nbheure'           => $ouvrage['totalHour'],
                            'total'             => $ouvrage['total'],
                            'order_cat_id'      => $orderCatId,
                            'ouvrage_prestation_id'=> $ouvrage['ouvrage_prestation_id'],
                            'ouvrage_toit_id'   => $ouvrage['ouvrage_toit_id'],
                            'ouvrage_metier_id' => $ouvrage['ouvrage_metier_id'],
                            'textcustomer'      => $ouvrage['customerText'],
                            'textchargeaffaire' => $ouvrage['textchargeaffaire'],
                            'textoperator'      => $ouvrage['textoperator'],
                            'type'              => 'SECURITE',
                            'name'              => $ouvrage['name'],
                            'created_at'        => Carbon::now(),
                            'updated_at'        => Carbon::now(),
                        ];
                        $orderOuvrageId = DB::table('order_ouvrages')->insertGetId($ouvrageData);
                        foreach ($ouvrage['tasks'] as $taskIndex => $task) {
                            $orderOuvrageTask = [
                                'order_ouvrage_id'      => $orderOuvrageId,
                                'name'                  => $task['name'],
                                'textcustomer'          => $task['customerText'],
                                'textchargeaffaire'     => $task['textchargeaffaire'],
                                'textoperator'          => $task['textoperator'],
                                'qty'                   => $task['qty'],
                                'unit_id'               => $task['unit_id'],
                                'created_at'            => Carbon::now(),
                                'updated_at'            => Carbon::now(),
                            ];
                            $orderOuvrageTaskId = DB::table('order_ouvrage_task')->insertGetId($orderOuvrageTask);
                            foreach ($task['details'] as $detailIndex => $detail) {
                                $detailData = [
                                    'order_ouvrage_task_id'   => $orderOuvrageTaskId,
                                    'product_id'        => $detail['productId'],
                                    'type'              => $detail['type'],
                                    'name'              => $detail['name'],
                                    'textcustomer'      => '',
                                    'textchargeaffaire' => '',
                                    'textoperator'      => '',
                                    'qty'               => $detail['qty'],
                                    'numberh'           => $detail['numberH'],
                                    'unitprice'         => $detail['unitPrice'],
                                    'qtyouvrage'        => $detail['qtyOuvrage'] == '' ? 0 : $detail['qtyOuvrage'],
                                    'qtyunitouvrage'    => 1,
                                    'houvrage'          => 0,
                                    'marge'             => $detail['marge'],
                                    'totalprice'        => $detail['totalPrice'],
                                    'taxe_id'           => $detail['tax'],
                                    'unit_id'           => $detail['unit_id'],
                                    'priceachat'        => $detail['unitPrice'],
                                    'original'          => $detail['original'] ?? false,
                                    'original_number_h' => $detail['originalNumberH'] ?? 1,
                                    'original_qty'      => $detail['originalDetailQty'] ?? 1,
                                    'created_at'        => Carbon::now(),
                                    'updated_at'        => Carbon::now(),
                                ];
                                if($detail['type'] == 'INTERIM'){
                                    $detailData['interim_societe_id']   = $detail['societe'];
                                }
                                if($detail['type'] == 'COMMANDE FOURNISSEUR'){
                                    $detailData['supplier_id']   = $detail['supplierId'];
                                    $detailData['datesupplier']   = $detail['datesupplier'];
                                    if(preg_match('/^data:application\/pdf;base64,/', $detail['base64'], $type)){
                                        $data = substr($detail['base64'], strpos($detail['base64'], ',') + 1);
                                        $type = 'pdf';
                                        if(!File::isDirectory(public_path('SupplierOrder/'))){
                                            File::makeDirectory(public_path('SupplierOrder/'), 0755, true, true);
                                        }
                                        $data = str_replace( ' ', '+', $data );
                                        $data = base64_decode($data);
                                        $uuid_filename = DB::select('select UUID() AS uuid')[0]->uuid;
                                        $orderFilePath = "/SupplierOrder/{$uuid_filename}.{$type}";
                                        Storage::disk('public')->put($orderFilePath, $data);
                                        $detailData['orderfile'] = $orderFilePath;
                                    }
                                }
                                DB::table('order_ouvrage_detail')->insert($detailData);
                            }
                        }
                    }
                }
                if( count($zone['prestationOuvrage']['ouvrages']) ){
                    $prestationCat = [
                        'order_zone_id' =>  $zoneId,
                        'type'          =>  'PRESTATION',
                        'name'          =>  $zone['prestationOuvrage']['name'],
                        'textcustomer'  =>  '',
                        'textoperator'  =>  '',
                        'created_at'    =>  Carbon::now(),
                        'updated_at'    =>  Carbon::now()
                    ];
                    $orderCatId = DB::table('order_cat')->insertGetId($prestationCat);
                    foreach ($zone['prestationOuvrage']['ouvrages'] as $ouvrage) {
                        $ouvrageData = [
                            'order_id'          => $orderId,
                            'order_zone_id'     => $zoneId,
                            'unit_id'           => $ouvrage['unit'],
                            'qty'               => $ouvrage['qty'],
                            'nbheure'           => $ouvrage['totalHour'],
                            'total'             => $ouvrage['total'],
                            'order_cat_id'      => $orderCatId,
                            'ouvrage_prestation_id'=> $ouvrage['ouvrage_prestation_id'],
                            'ouvrage_toit_id'   => $ouvrage['ouvrage_toit_id'],
                            'ouvrage_metier_id' => $ouvrage['ouvrage_metier_id'],
                            'textcustomer'      => $ouvrage['customerText'],
                            'textchargeaffaire' => $ouvrage['textchargeaffaire'],
                            'textoperator'      => $ouvrage['textoperator'],
                            'type'              => 'PRESTATION',
                            'name'              => $ouvrage['name'],
                            'created_at'        => Carbon::now(),
                            'updated_at'        => Carbon::now(),
                        ];
                        $orderOuvrageId = DB::table('order_ouvrages')->insertGetId($ouvrageData);
                        foreach ($ouvrage['tasks'] as $taskIndex => $task) {
                            $orderOuvrageTask = [
                                'order_ouvrage_id'      => $orderOuvrageId,
                                'name'                  => $task['name'],
                                'textcustomer'          => $task['customerText'],
                                'textchargeaffaire'     => $task['textchargeaffaire'],
                                'textoperator'          => $task['textoperator'],
                                'qty'                   => $task['qty'],
                                'unit_id'               => $task['unit_id'],
                                'created_at'            => Carbon::now(),
                                'updated_at'            => Carbon::now(),
                            ];
                            $orderOuvrageTaskId = DB::table('order_ouvrage_task')->insertGetId($orderOuvrageTask);
                            foreach ($task['details'] as $detailIndex => $detail) {
                                $detailData = [
                                    'order_ouvrage_task_id'   => $orderOuvrageTaskId,
                                    'product_id'        => $detail['productId'],
                                    'type'              => $detail['type'],
                                    'name'              => $detail['name'],
                                    'textcustomer'      => '',
                                    'textchargeaffaire' => '',
                                    'textoperator'      => '',
                                    'qty'               => $detail['qty'],
                                    'numberh'           => $detail['numberH'],
                                    'unitprice'         => $detail['unitPrice'],
                                    'qtyouvrage'        => $detail['qtyOuvrage'] == '' ? 0 : $detail['qtyOuvrage'],
                                    'qtyunitouvrage'    => 1,
                                    'houvrage'          => 0,
                                    'marge'             => $detail['marge'],
                                    'totalprice'        => $detail['totalPrice'],
                                    'taxe_id'           => $detail['tax'],
                                    'unit_id'           => $detail['unit_id'],
                                    'priceachat'        => $detail['unitPrice'],
                                    'original'          => $detail['original'] ?? false,
                                    'original_number_h' => $detail['originalNumberH'] ?? 1,
                                    'original_qty'      => $detail['originalDetailQty'] ?? 1,
                                    'created_at'        => Carbon::now(),
                                    'updated_at'        => Carbon::now(),
                                ];
                                if($detail['type'] == 'INTERIM'){
                                    $detailData['interim_societe_id']   = $detail['societe'];
                                }
                                if($detail['type'] == 'COMMANDE FOURNISSEUR'){
                                    $detailData['supplier_id']   = $detail['supplierId'];
                                    $detailData['datesupplier']   = $detail['datesupplier'];
                                    if(preg_match('/^data:application\/pdf;base64,/', $detail['base64'], $type)){
                                        $data = substr($detail['base64'], strpos($detail['base64'], ',') + 1);
                                        $type = 'pdf';
                                        if(!File::isDirectory(public_path('SupplierOrder/'))){
                                            File::makeDirectory(public_path('SupplierOrder/'), 0755, true, true);
                                        }
                                        $data = str_replace( ' ', '+', $data );
                                        $data = base64_decode($data);
                                        $uuid_filename = DB::select('select UUID() AS uuid')[0]->uuid;
                                        $orderFilePath = "/SupplierOrder/{$uuid_filename}.{$type}";
                                        Storage::disk('public')->put($orderFilePath, $data);
                                        $detailData['orderfile'] = $orderFilePath;
                                    }
                                }
                                DB::table('order_ouvrage_detail')->insert($detailData);
                            }
                        }
                    }
                }
            }
            // store the devis that has no ouvrages
        }else{
            $orderData = [
                'lang_id'           => 1,
                'name'              => $request->orderName,
                'affiliate_id'      => Auth::user()->affiliate->id,
                'responsable_id'    => Auth::id(),
                'order_state_id'    => 2,
                'total'             => ($request->totalAmount),
                'nbheure'           => ($request->totalHours),
                'address_id'        => $request->address['id'],
                'customer_id'       => $request->customer['id'],
                'contact_id'        => $request->customer['contact_id'],
                'remise'            => $request->reductionAmount,
                'datecommande'      => Carbon::now(),
                'signed_by_customer'=> 0,
                'reference'         => $this->passwdGen(10,'NO_NUMERIC'),
                'created_at'        => Carbon::now(),
                'updated_at'        => Carbon::now(),
            ];
            $orderId = DB::table('orders')->insertGetId($orderData);
            $orderHistory = [
                'user_id'       => Auth::id(),
                'order_id'      => $orderId,
                'order_state_id'=> 2,
                'created_at'    => Carbon::now(),
                'updated_at'    => Carbon::now(),
            ];
            DB::table('order_histories')->insert($orderHistory);
            if(
                ! Ged::where('user_id','=',Auth::id())
                    ->where('customer_id', $request->customer['id'])
                    ->where('order_id', $orderId)->exists()
            ){
                $zedData = [
                    'customer_id'   =>  $request->customer['id'],
                    'user_id'       =>  Auth::id(),
                    'order_id'      =>  $orderId,
                    'created_at'    =>  Carbon::now(),
                    'updated_at'    =>  Carbon::now(),
                ];
                $gedId = DB::table('geds')->insertGetId($zedData);
            }else{
                $gedId = Ged::where('user_id','=',Auth::id())
                            ->where('customer_id', $request->customer['id'])
                            ->where('order_id', $orderId)->value('id');
            }
            $zoneData = [
                'order_id'      =>  $orderId,
                'latitude'      =>  $request->address['lat'],
                'longitude'     =>  $request->address['lon'],
                'description'   =>  '',
                'hauteur'       =>  0,
                'name'          =>  'Zone 1',
                'created_at'    =>  Carbon::now(),
                'updated_at'    =>  Carbon::now(),
            ];
            $zoneId = DB::table('order_zones')->insertGetId($zoneData);
            // save photos
            foreach ($request->photos as $file) {
                $gedDetail = new GedDetail();
                $gedDetail->ged_id = $gedId;
                $gedDetail->order_zone_id = $zoneId;
                $gedDetail->ged_category_id = 0;
                $gedDetail->user_id = Auth::id();
                $gedDetail->save();
                $gedDetail = $gedDetail->fresh();//retreve fresh object with all fields

                if( $gedDetail->file == null){//files can only be stored once to avoid duplicates;
                    if( !empty($file['base64data']) ){
                        $storedFile = $this->storeFile($file['base64data'], $file['fileName'], $gedDetail->id);
                        $gedDetail->file = $storedFile->file;
                        $gedDetail->type = $storedFile->type;
                        $gedDetail->storage_path = $storedFile->storage_path;
                        $gedDetail->human_readable_filename = $storedFile->human_readable_filename;
                    }
                }
                $gedDetail->save();
            }
            // save order details
            foreach ($request->details as $detail) {
                $orderDetailData = [
                    'order_id'      => $orderId,
                    'supplier_id'   => 0,
                    'name'          => $detail['name'],
                    'unitprice'     => $detail['unitPrice'],
                    'taxe_id'       => $detail['taxId'],
                    'qty'           => $detail['qty'],
                    'numberh'       => $detail['hours'],
                ];
                DB::table('order_detail')->insert($orderDetailData);
            }
            // save order documents
            foreach ($request->documents as $document) {
                $orderDocument          =   new OrderDocument();
                $orderDocument->ged_id  =   $gedId;
                $orderDocument->human_readable_filename =   $document->getClientOriginalName();
                $uuid_filename = DB::select('select UUID() AS uuid')[0]->uuid;
                $orderDocument->name    =   $uuid_filename.'.'.$document->getClientOriginalExtension();
                $orderDocument->file_path   =   $document->storeAs( 'GED/GED_' . $gedId . '/order_documents', $orderDocument->name, 'public' );
                $orderDocument->save();
            }
        }
        return response()->json(['success'=> true, 'orderId' => $orderId]);
    }

    /**
     * Get Devis details
     *
     */
    public function getDevis(Order $order){
        $zones = DB::table('order_zones')
                    ->where('order_id', $order->id)
                    ->where(function($query){
                            $query->whereNull('deleted_at')->orWhere('deleted_at', '0000-00-00 00:00:00');
                        })
                    ->select('id', 'latitude as lat', 'longitude as lon', 'order_id', 'name', 'hauteur as height', 'moyenacces_id as roofAccess')
                    ->get();
        $devis = [];
        $devis['orderStatus'] = DB::table('orders')
                                ->join('order_states', 'orders.order_state_id', '=', 'order_states.id')
                                ->where('orders.id', $order->id)
                                ->select('order_states.order_type as type', 'order_states.name', 'order_states.fontcolor', 'order_states.color')->first();
        $devis['orderName'] = $order->name;
        $devis['describeOn'] = DB::table('settings')->where('key', 'admin.Describe')->value('value') == 1 ? true : false;
        $devis['totalHoursForInstall'] = 0;
        $devis['totalPriceForInstall'] = 0;
        $devis['totalHoursForSecurity'] = 0;
        $devis['totalPriceForSecurity'] = 0;
        $devis['totalHoursForPrestation'] = 0;
        $devis['totalPriceForPrestation'] = 0;
        $devis['totalHoursForInterim'] = 0;
        $devis['totalPriceWithoutMarge'] = 0;
        $devis['totalUnitPrice'] = 0;
        $devis['discount'] = $order->remise;
        $devis['customer'] = DB::table('customers')
                                ->join('group', 'group.id', '=', 'customers.group_id')
                                ->join('taxes', 'taxes.id', '=', 'customers.taxe_id')
                                ->join('contacts', 'contacts.customer_id', '=', 'customers.id')
                                ->where('customers.id', $order->customer_id)
                                ->select('customers.id', 'customers.company', 'customers.raisonsociale', 'group.Name as group'
                                    ,DB::raw('CONCAT(customers.firstname, " ",customers.name) as contact'),
                                    'customers.telephone', 'taxes.Name as tax', 'taxes.id as taxId', 'customers.naf', 'customers.siret', 'contacts.id as contact_id'
                                )->first();
        $devis['address'] = DB::table('addresses')
                                // ->join('group', 'group.id', '=', 'customers.group_id')
                                ->where('id', $order->address_id)
                                ->select(
                                    'id', 'address1', 'address2', 'postcode', 'city', 'address_type_id as addressType',
                                    'latitude as lat', 'longitude as lon'
                                )->first();
        foreach ($zones as $zoneIndex => $zone) {
            // get ged details
            $devis['zones'][$zoneIndex]['id'] = $zone->id;
            $devis['zones'][$zoneIndex]['height'] = $zone->height;
            $devis['zones'][$zoneIndex]['name'] = $zone->name;
            $devis['zones'][$zoneIndex]['roofAccess'] = $zone->roofAccess;
            $devis['zones'][$zoneIndex]['lat'] = $zone->lat;
            $devis['zones'][$zoneIndex]['lon'] = $zone->lon;

            $devis['zones'][$zoneIndex]['edit'] = false;
            $categories = DB::table('ged_categories')->select('id', 'name')->get();
            foreach ($categories as $item) {
                $ged_details = DB::table('ged_details')
                                ->where('order_zone_id', $zone->id)
                                ->where('ged_category_id', $item->id)
                                ->whereNotIn('type', ['txt', 'description'])
                                ->where('user_id', Auth::id())
                                ->select(
                                    DB::raw('CONCAT(CONCAT(storage_path, "/",file), ".", type) as url'),
                                    'human_readable_filename as fileName',
                                    'id', 'type'
                                )->get();
                if($ged_details->count() > 0){
                    foreach ($ged_details as $ged_detail) {
                        $item->items[] = [
                            'base64data'    => '',
                            'fileName'      => $ged_detail->fileName,
                            'url'           => getenv('APP_URL').Storage::url($ged_detail->url),
                            'id'            => $ged_detail->id,
                            'type'          => $ged_detail->type,
                        ];
                    }
                }else{
                    $item->items = [];
                }
            }
            $devis['zones'][$zoneIndex]['gedCats'] = $categories->groupBy('id');
            if($devis['describeOn'] && DB::table('order_describe')->where('order_zone_id', $zone->id)->count()){
                $describes = DB::table('order_describe')->join('describe', 'describe.id', '=', 'order_describe.describe_id')
                                ->where('order_zone_id', $zone->id)
                                ->join('describe_categories', 'describe_categories.id', '=', 'describe.describe_category_id')
                                ->select(
                                    'describe.name', 'describe.type', 'order_describe.value as default',
                                    'describe.order', 'describe.data', 'describe_categories.name as catName',
                                    'describe.id', 'order_describe.id as order_describe_id'
                                )
                                ->orderBy('order')->get();
            }else{
                $describes = DB::table('describe')->join('describe_categories', 'describe.describe_category_id', '=', 'describe_categories.id')
                            ->select('describe.id', 'describe.name', 'describe.type', 'describe.default', 'describe.order', 'describe.data', 'describe_categories.name as catName')
                            ->orderBy('order')->get();
            }
            foreach($describes as $describe){
                $describe->data = json_decode($describe->data);
                if($describe->type == 'Checkbox' || $describe->type == 'Switch'){
                    $describe->default = $describe->default == 0 ? false : true;
                }
            }
            $describes = $describes->groupBy('catName');
            $devis['zones'][$zoneIndex]['describes'] = $describes->groupBy('catName');

            if($devis['describeOn'] && DB::table('order_services')->where('order_zone_id', $zone->id)->count()){
                $services = DB::table('order_services')->join('services', 'services.id', '=', 'order_services.service_id')
                ->where('order_zone_id', $zone->id)
                ->select(
                    'services.name', 'services.type', 'order_services.value',
                    'services.data', 'order_services.active', 'order_services.semti',
                    'services.id', 'order_services.id as order_service_id', 'order_services.sstt',
                    'order_services.client', 'order_services.loc',
                )
                ->orderBy('order')->get();
            }else{
                $services = DB::table('services')->select(
                    'services.name', 'services.type', 'services.default as value',
                    'services.data', 'services.id',
                )
                ->orderBy('order')->get();
            }
            foreach($services as $service){
                $service->data = json_decode($service->data);
                if($service->type == 'Checkbox' || $service->type == 'Switch'){
                    $service->value = $service->value == 0 ? false : true;
                }
                if(isset($service->active)){
                    $service->active = $service->active == 0 ? false : true;
                    $service->semti = $service->semti == 0 ? false : true;
                    $service->sstt = $service->sstt == 0 ? false : true;
                    $service->client = $service->client == 0 ? false : true;
                    $service->loc = $service->loc == 0 ? false : true;
                }else{
                    $service->active = false;
                    $service->semti = false;
                    $service->sstt = false;
                    $service->client = false;
                    $service->loc = false;
                }
            }
            $devis['zones'][$zoneIndex]['services'] = $services;
            //installation ouvrages;

            $orderCat = DB::table('order_cat')->where('order_zone_id', $zone->id)->where('type', 'INSTALLATION')->first();
            $devis['zones'][$zoneIndex]['installOuvrage']['sumUnitPrice'] = 0;
            $devis['zones'][$zoneIndex]['installOuvrage']['name'] = 'Installation';
            $devis['zones'][$zoneIndex]['installOuvrage']['edit'] = false;
            $devis['zones'][$zoneIndex]['installOuvrage']['totalHour'] = 0;
            $devis['zones'][$zoneIndex]['installOuvrage']['sumUnitPrice'] = 0;
            $devis['zones'][$zoneIndex]['installOuvrage']['totalPrice'] = 0;
            if($orderCat){
                $devis['zones'][$zoneIndex]['installOuvrage']['name'] = $orderCat->name;
                $ouvrages = DB::table('order_ouvrages')
                    ->where('order_id', $order->id)
                    ->where('order_zone_id', $zone->id)
                    ->where('order_cat_id', $orderCat->id)
                    ->where(function($query){
                        $query->whereNull('deleted_at')->orWhere('deleted_at', '0000-00-00 00:00:00');
                    })
                    ->where('type', 'INSTALLATION')
                    ->select(
                        'id', 'unit_id as unit', 'qty', 'ouvrage_prestation_id', 'ouvrage_metier_id', 'ouvrage_toit_id',
                        'textcustomer as customerText','textchargeaffaire', 'textoperator', 'name',
                        'type', 'qty', 'qty as qtyOuvrage', 'nbheure as totalHour', 'total'
                    )
                    ->get();
                foreach ($ouvrages as $ouvrage) {
                    $ouvrage->avg = 0;
                    $ouvrage->totalWithoutMarge = 0;
                    $tasks = DB::table('order_ouvrage_task')
                                ->where('order_ouvrage_id', $ouvrage->id)
                                ->where(function($query){
                                    $query->whereNull('deleted_at')->orWhere('deleted_at', '0000-00-00 00:00:00');
                                })
                                ->select(
                                    'id', 'name', 'textcustomer as customerText', 'textchargeaffaire',
                                    'textoperator', 'unit_id', 'qty'
                                )->get();
                    foreach ($tasks as $task) {
                        $details = DB::table('order_ouvrage_detail')
                        ->leftJoin('units', 'units.id', '=', 'order_ouvrage_detail.unit_id')
                        ->where('order_ouvrage_task_id', $task->id)
                        ->where(function($query){
                            $query->whereNull('order_ouvrage_detail.deleted_at')->orWhere('order_ouvrage_detail.deleted_at', '0000-00-00 00:00:00');
                        })
                        ->select(
                            'order_ouvrage_detail.id', 'order_ouvrage_detail.numberh as numberH', 'order_ouvrage_detail.qty', 'order_ouvrage_detail.unit_id',
                            'order_ouvrage_detail.type', 'units.code as unit', 'order_ouvrage_detail.product_id as productId', 'order_ouvrage_detail.name', 'order_ouvrage_detail.taxe_id as tax', 'order_ouvrage_detail.unitprice as unitPrice', 'order_ouvrage_detail.datesupplier',
                            'order_ouvrage_detail.supplier_id as supplierId', 'order_ouvrage_detail.interim_societe_id as societe', 'order_ouvrage_detail.marge', 'order_ouvrage_detail.totalPrice', 'order_ouvrage_detail.qty', 'order_ouvrage_detail.qty as qtyOuvrage', 'order_ouvrage_detail.original', 'order_ouvrage_detail.original_number_h as originalNumberH',
                            'order_ouvrage_detail.original_qty as originalDetailQty', 'order_ouvrage_detail.orderfile as base64'
                        )->get();
                        foreach ($details as $detail) {
                            if($detail->type == 'COMMANDE FOURNISSEUR'){
                                $detail->base64 = getenv('APP_URL').Storage::url($detail->base64);
                            }
                            $detail->totalPriceWithoutMarge = 0;
                            $ouvrage->totalHour += $detail->numberH;
                            $devis['totalPriceForInstall'] += $detail->totalPrice;
                            $devis['zones'][$zoneIndex]['installOuvrage']['totalPrice'] += $detail->totalPrice;
                        }
                        $task->details = $details;
                    }
                    $devis['zones'][$zoneIndex]['installOuvrage']['totalHour'] = $ouvrage->totalHour;
                    $ouvrage->tasks = $tasks;
                }
                $devis['zones'][$zoneIndex]['installOuvrage']['ouvrages'] = $ouvrages;
            }else{
                $devis['zones'][$zoneIndex]['installOuvrage']['ouvrages'] = [];
            }

            //securite ouvrages;
            $orderCat = DB::table('order_cat')->where('order_zone_id', $zone->id)->where('type', 'SECURITE')->first();
            $devis['zones'][$zoneIndex]['securityOuvrage']['sumUnitPrice'] = 0;
            $devis['zones'][$zoneIndex]['securityOuvrage']['name'] = 'Sécurité';
            $devis['zones'][$zoneIndex]['securityOuvrage']['edit'] = false;
            $devis['zones'][$zoneIndex]['securityOuvrage']['totalHour'] = 0;
            $devis['zones'][$zoneIndex]['securityOuvrage']['sumUnitPrice'] = 0;
            $devis['zones'][$zoneIndex]['securityOuvrage']['totalPrice'] = 0;
            if($orderCat){
                $devis['zones'][$zoneIndex]['securityOuvrage']['name'] = $orderCat->name;
                $ouvrages = DB::table('order_ouvrages')
                    ->where('order_id', $order->id)
                    ->where('order_zone_id', $zone->id)
                    ->where('order_cat_id', $orderCat->id)
                    ->where(function($query){
                            $query->whereNull('deleted_at')->orWhere('deleted_at', '0000-00-00 00:00:00');
                        })
                    ->where('type', 'SECURITE')
                    ->select(
                        'id', 'unit_id as unit', 'qty', 'ouvrage_prestation_id', 'ouvrage_metier_id', 'ouvrage_toit_id',
                        'textcustomer as customerText','textchargeaffaire', 'textoperator', 'name',
                        'type', 'qty', 'qty as qtyOuvrage', 'nbheure as totalHour', 'total'
                    )
                    ->get();
                foreach ($ouvrages as $ouvrage) {
                    $ouvrage->avg = 0;
                    $ouvrage->totalWithoutMarge = 0;
                    $tasks = DB::table('order_ouvrage_task')
                                ->where('order_ouvrage_id', $ouvrage->id)
                                ->where(function($query){
                            $query->whereNull('deleted_at')->orWhere('deleted_at', '0000-00-00 00:00:00');
                        })
                                ->select('id', 'name', 'textcustomer as customerText', 'textchargeaffaire', 'textoperator', 'unit_id', 'qty')->get();
                    foreach ($tasks as $task) {
                        $details = DB::table('order_ouvrage_detail')
                        ->leftJoin('units', 'units.id', '=', 'order_ouvrage_detail.unit_id')
                        ->where('order_ouvrage_task_id', $task->id)
                        ->where(function($query){
                            $query->whereNull('order_ouvrage_detail.deleted_at')->orWhere('order_ouvrage_detail.deleted_at', '0000-00-00 00:00:00');
                        })
                        ->select(
                            'order_ouvrage_detail.id', 'order_ouvrage_detail.numberh as numberH', 'order_ouvrage_detail.qty', 'order_ouvrage_detail.unit_id',
                            'order_ouvrage_detail.type', 'units.code as unit', 'order_ouvrage_detail.product_id as productId', 'order_ouvrage_detail.name', 'order_ouvrage_detail.taxe_id as tax', 'order_ouvrage_detail.unitprice as unitPrice', 'order_ouvrage_detail.datesupplier',
                            'order_ouvrage_detail.supplier_id as supplierId', 'order_ouvrage_detail.interim_societe_id as societe', 'order_ouvrage_detail.marge', 'order_ouvrage_detail.totalPrice', 'order_ouvrage_detail.qty', 'order_ouvrage_detail.qty as qtyOuvrage', 'order_ouvrage_detail.original', 'order_ouvrage_detail.original_number_h as originalNumberH',
                            'order_ouvrage_detail.original_qty as originalDetailQty', 'order_ouvrage_detail.orderfile as base64'
                        )->get();
                        foreach ($details as $detail) {
                            if($detail->type == 'COMMANDE FOURNISSEUR'){
                                $detail->base64 = getenv('APP_URL').Storage::url($detail->base64);
                            }
                            $detail->totalPriceWithoutMarge = 0;
                            $ouvrage->totalHour += $detail->numberH;
                            $devis['totalPriceForInstall'] += $detail->totalPrice;
                            $devis['zones'][$zoneIndex]['installOuvrage']['totalPrice'] += $detail->totalPrice;
                        }
                        $task->details = $details;
                    }
                    $devis['zones'][$zoneIndex]['securityOuvrage']['totalHour'] = $ouvrage->totalHour;
                    $ouvrage->tasks = $tasks;
                }
                $devis['zones'][$zoneIndex]['securityOuvrage']['ouvrages'] = $ouvrages;
            }else{
                $devis['zones'][$zoneIndex]['securityOuvrage']['ouvrages'] = [];
            }

            //prestation ouvrages;
            $orderCat = DB::table('order_cat')->where('order_zone_id', $zone->id)->where('type', 'PRESTATION')->first();
            $devis['zones'][$zoneIndex]['prestationOuvrage']['sumUnitPrice'] = 0;
            $devis['zones'][$zoneIndex]['prestationOuvrage']['name'] = 'PRESTATION';
            $devis['zones'][$zoneIndex]['prestationOuvrage']['edit'] = false;
            $devis['zones'][$zoneIndex]['prestationOuvrage']['totalHour'] = 0;
            $devis['zones'][$zoneIndex]['prestationOuvrage']['sumUnitPrice'] = 0;
            $devis['zones'][$zoneIndex]['prestationOuvrage']['totalPrice'] = 0;
            if($orderCat){
                $devis['zones'][$zoneIndex]['prestationOuvrage']['name'] = $orderCat->name;
                $ouvrages = DB::table('order_ouvrages')
                    ->where('order_id', $order->id)
                    ->where('order_zone_id', $zone->id)
                    ->where('order_cat_id', $orderCat->id)
                    ->where(function($query){
                            $query->whereNull('deleted_at')->orWhere('deleted_at', '0000-00-00 00:00:00');
                        })
                    ->where('type', 'PRESTATION')
                    ->select(
                        'id', 'unit_id as unit', 'qty', 'ouvrage_prestation_id', 'ouvrage_metier_id', 'ouvrage_toit_id',
                        'textcustomer as customerText','textchargeaffaire', 'textoperator', 'name',
                        'type', 'qty', 'qty as qtyOuvrage', 'nbheure as totalHour', 'total'
                    )
                    ->get();
                foreach ($ouvrages as $ouvrage) {
                    $ouvrage->avg = 0;
                    $ouvrage->totalWithoutMarge = 0;
                    $tasks = DB::table('order_ouvrage_task')
                            ->where('order_ouvrage_id', $ouvrage->id)
                            ->where(function($query){
                                $query->whereNull('deleted_at')->orWhere('deleted_at', '0000-00-00 00:00:00');
                            })
                            ->select('id', 'name', 'textcustomer as customerText', 'textchargeaffaire', 'textoperator', 'unit_id', 'qty')->get();
                    foreach ($tasks as $task) {
                        $details = DB::table('order_ouvrage_detail')
                        ->leftJoin('units', 'units.id', '=', 'order_ouvrage_detail.unit_id')
                        ->where('order_ouvrage_task_id', $task->id)
                        ->where(function($query){
                            $query->whereNull('order_ouvrage_detail.deleted_at')->orWhere('order_ouvrage_detail.deleted_at', '0000-00-00 00:00:00');
                        })
                        ->select(
                            'order_ouvrage_detail.id', 'order_ouvrage_detail.numberh as numberH', 'order_ouvrage_detail.qty', 'order_ouvrage_detail.unit_id',
                            'order_ouvrage_detail.type', 'units.code as unit', 'order_ouvrage_detail.product_id as productId', 'order_ouvrage_detail.name', 'order_ouvrage_detail.taxe_id as tax', 'order_ouvrage_detail.unitprice as unitPrice', 'order_ouvrage_detail.datesupplier',
                            'order_ouvrage_detail.supplier_id as supplierId', 'order_ouvrage_detail.interim_societe_id as societe', 'order_ouvrage_detail.marge', 'order_ouvrage_detail.totalPrice', 'order_ouvrage_detail.qty', 'order_ouvrage_detail.qty as qtyOuvrage', 'order_ouvrage_detail.original', 'order_ouvrage_detail.original_number_h as originalNumberH',
                            'order_ouvrage_detail.original_qty as originalDetailQty', 'order_ouvrage_detail.orderfile as base64'
                        )->get();
                        foreach ($details as $detail) {
                            if($detail->type == 'COMMANDE FOURNISSEUR'){
                                $detail->base64 = getenv('APP_URL').Storage::url($detail->base64);
                            }
                            $detail->totalPriceWithoutMarge = 0;
                            $ouvrage->totalHour += $detail->numberH;
                            $devis['totalPriceForInstall'] += $detail->totalPrice;
                            $devis['zones'][$zoneIndex]['installOuvrage']['totalPrice'] += $detail->totalPrice;
                        }
                        $task->details = $details;
                    }
                    $devis['zones'][$zoneIndex]['prestationOuvrage']['totalHour'] = $ouvrage->totalHour;
                    $ouvrage->tasks = $tasks;
                }
                $devis['zones'][$zoneIndex]['prestationOuvrage']['ouvrages'] = $ouvrages;
            }else{
                $devis['zones'][$zoneIndex]['prestationOuvrage']['ouvrages'] = [];
            }
        }
        $categories = DB::table('ged_categories')->select('id', 'name')->get();
        foreach ($categories as $item) {
            $item->items = [];
        }
        return response()->json(
            [
                'devis' => $devis,
                'useGoogleService'   => DB::table('settings')->where('key', 'admin.Google')->value('value') == 1 ? true : false,
                'gedCats'   => $categories->groupBy('id'),
                'units'     => DB::table('units')->select('id as value', 'code as display')->get(),
                'taxes'     => DB::table('taxes')->select('id as value', DB::raw('CEIL(taux * 100) as display'))->get(),
                'roofAccesses'     => DB::table('moyenacces')->select('id as value', 'name as display')->get(),
            ]
        );
    }

    /**
     * update a devis
     */
    public function updateDevis(Request $request, $orderId){
        $orderData = [
            'name'              => $request->orderName,
            'affiliate_id'      => Auth::user()->affiliate->id,
            'responsable_id'    => Auth::id(),
            'total'             => ($request->totalPriceForInstall + $request->totalPriceForSecurity + $request->totalPriceForPrestation),
            'remise'            => $request->discount,
            'address_id'        => $request->address['id'],
            'customer_id'       => $request->customer['id'],
            'updated_at'        => Carbon::now(),
        ];

        DB::table('orders')->where('id', $orderId)->update($orderData);

        $gedId = Ged::where('user_id','=',Auth::id())
                    ->where('customer_id', $request->customer['id'])
                    ->where('order_id', $orderId)->value('id');
        $availOrderZoneIds = [];
        foreach ($request->zones as $zone) {
            $zoneId = $zone['id'];
            $zoneData = [
                'order_id'      =>  $orderId,
                'latitude'      =>  $request->address['lat'],
                'longitude'     =>  $request->address['lon'],
                'hauteur'       =>  $zone['height'],
                'moyenacces_id' =>  $zone['roofAccess'],
                'name'          =>  $zone['name'],
                'updated_at'    =>  Carbon::now(),
            ];
            if($zoneId != ''){
                DB::table('order_zones')->where('id', $zoneId)->update($zoneData);
            }else{
                $zoneData = [
                    'description'   =>  '',
                    'created_at'    =>  Carbon::now(),
                ];
                $zoneId = DB::table('order_zones')->insertGetId($zoneData);
            }
            // adding Order Zone's id;
            $availOrderZoneIds[] = $zoneId;
            // save ged details
            foreach ($zone['gedCats'] as $gedCatId=> $gedCat) {
                foreach ($gedCat[0]['items'] as $file) {
                    if($file['id'] == ''){
                        $gedDetail = new GedDetail();
                    }else{
                        $gedDetail = GedDetail::find($file['id']);
                    }
                    $gedDetail->ged_id = $gedId;
                    $gedDetail->order_zone_id = $zoneId;
                    $gedDetail->ged_category_id = $gedCatId;
                    $gedDetail->user_id = Auth::id();
                    $gedDetail->save();
                    $gedDetail = $gedDetail->fresh();//retreve fresh object with all fields

                    // if( $gedDetail->file == null){//files can only be stored once to avoid duplicates;
                    if( !empty($file['base64data']) ){
                        $storedFile = $this->storeFile($file['base64data'], $file['fileName'], $gedDetail->id);
                        $gedDetail->file = $storedFile->file;
                        $gedDetail->type = $storedFile->type;
                        $gedDetail->storage_path = $storedFile->storage_path;
                        $gedDetail->human_readable_filename = $storedFile->human_readable_filename;
                    }
                    // }
                    $gedDetail->save();
                }
            }
            // update describes
            foreach($zone['describes'] as $zoneDescribes) {
                foreach ($zoneDescribes as $describe) {
                    if(isset($describe['order_describe_id'])){
                        DB::table('order_describe')->where('id', $describe['order_describe_id'])->update([
                            'describe_id'=> $describe['id'],
                            'value'=> $describe['default'],
                            'order_zone_id'=> $zoneId,
                            'updated_at'=> now()
                        ]);
                    }else{
                        DB::table('order_describe')->insert([
                            'order_zone_id'=> $zoneId,
                            'describe_id'=> $describe['id'],
                            'value'=> $describe['default'],
                            'created_at'=> now(),
                            'updated_at'=> now()
                        ]);
                    }
                }
            }
            // update services
            foreach($zone['services'] as $service) {
                if(isset($service['order_service_id'])){
                    DB::table('order_services')->where('id', $service['order_service_id'])->update([
                        'service_id'=> $service['id'],
                        'value'=> $service['value'],
                        'active'=> $service['active'],
                        'semti'=> $service['semti'],
                        'sstt'=> $service['sstt'],
                        'loc'=> $service['loc'],
                        'client'=> $service['client'],
                        'order_zone_id'=> $zoneId,
                        'updated_at'=> now()
                    ]);
                }else{
                    DB::table('order_services')->insert([
                        'order_zone_id'=> $zoneId,
                        'service_id'=> $service['id'],
                        'value'=> $service['value'],
                        'active'=> $service['active'],
                        'semti'=> $service['semti'],
                        'sstt'=> $service['sstt'],
                        'loc'=> $service['loc'],
                        'client'=> $service['client'],
                        'created_at'=> now(),
                        'updated_at'=> now()
                    ]);
                }
            }
            if( count($zone['installOuvrage']['ouvrages']) ){
                $orderCat = DB::table('order_cat')
                            ->where('order_zone_id', $zoneId)
                            ->where('type', 'INSTALLATION')
                            ->first();
                $installationCat = [
                    'name'          =>  $zone['installOuvrage']['name'],
                    'updated_at'    =>  Carbon::now(),
                ];
                if($orderCat){
                    $orderCatId = $orderCat->id;
                    DB::table('order_cat')->where('id', $orderCat->id)->update($installationCat);
                }else{
                    $installationCat['order_zone_id'] =  $zoneId;
                    $installationCat['type']          =  'INSTALLATION';
                    $installationCat['textcustomer']  =  '';
                    $installationCat['textoperator']  =  '';
                    $installationCat['created_at']    =  Carbon::now();
                    $orderCatId = DB::table('order_cat')->insertGetId($installationCat);
                }
                // declare var for available installation ouvrage
                $availInstallOuvrages = [];
                foreach ($zone['installOuvrage']['ouvrages'] as $ouvrage) {
                    $ouvrageData = [
                        'order_id'          => $orderId,
                        'order_zone_id'     => $zoneId,
                        'order_cat_id'      => $orderCatId,
                        'unit_id'           => $ouvrage['unit'],
                        'qty'               => $ouvrage['qty'],
                        'nbheure'           => $ouvrage['totalHour'],
                        'total'             => $ouvrage['total'],
                        'ouvrage_prestation_id'=> $ouvrage['ouvrage_prestation_id'],
                        'ouvrage_toit_id'   => $ouvrage['ouvrage_toit_id'],
                        'ouvrage_metier_id' => $ouvrage['ouvrage_metier_id'],
                        'textcustomer'      => $ouvrage['customerText'],
                        'textchargeaffaire' => $ouvrage['textchargeaffaire'],
                        'textoperator'      => $ouvrage['textoperator'],
                        'name'              => $ouvrage['name'],
                        'updated_at'        => Carbon::now(),
                    ];
                    if(isset($ouvrage['id']) && $ouvrage['id'] != ''){
                        $orderOuvrageId = $ouvrage['id'];
                        DB::table('order_ouvrages')->where('id', $orderOuvrageId)->update($ouvrageData);
                    }else{
                        $ouvrageData['type']            = 'INSTALLATION';
                        $ouvrageData['created_at']      = Carbon::now();
                        $orderOuvrageId = DB::table('order_ouvrages')->insertGetId($ouvrageData);
                    }
                    // adding ouvrage's id;
                    $availInstallOuvrages[] = $orderOuvrageId;
                    // declare var for available ouvrage tasks
                    $avileOuvrageTasks = [];
                    foreach ($ouvrage['tasks'] as $taskIndex => $task) {
                        $orderOuvrageTask = [
                            'order_ouvrage_id'      => $orderOuvrageId,
                            'name'                  => $task['name'],
                            'textcustomer'          => $task['customerText'],
                            'textchargeaffaire'     => $task['textchargeaffaire'],
                            'textoperator'          => $task['textoperator'],
                            'qty'                   => $task['qty'],
                            'unit_id'               => $task['unit_id'],
                            'updated_at'            =>  Carbon::now(),
                        ];
                        if(isset($task['id']) && $task['id'] != ''){
                            $orderOuvrageTaskId = $task['id'];
                            DB::table('order_ouvrage_task')->where('id', $task['id'])->update($orderOuvrageTask);
                        }else{
                            $orderOuvrageTask['created_at'] = Carbon::now();
                            $orderOuvrageTaskId = DB::table('order_ouvrage_task')->insertGetId($orderOuvrageTask);
                        }
                        // adding task's id;
                        $avileOuvrageTasks[] = $orderOuvrageTaskId;
                        $availTaskDetails = [];
                        foreach ($task['details'] as $detailIndex => $detail) {
                            $detailData = [
                                'order_ouvrage_task_id'   => $orderOuvrageTaskId,
                                'product_id'        => $detail['productId'],
                                'type'              => $detail['type'],
                                'name'              => $detail['name'],
                                'textcustomer'      => '',
                                'textchargeaffaire' => '',
                                'textoperator'      => '',
                                'qty'               => $detail['qty'],
                                'numberh'           => $detail['numberH'],
                                'unitprice'         => $detail['unitPrice'],
                                'qtyouvrage'        => $detail['qtyOuvrage'] == '' ? 0 : $detail['qtyOuvrage'],
                                'qtyunitouvrage'    => 1,
                                'houvrage'          => 0,
                                'marge'             => $detail['marge'],
                                'totalprice'        => $detail['totalPrice'],
                                'taxe_id'           => $detail['tax'],
                                'unit_id'           => $detail['unit_id'],
                                'priceachat'        => $detail['unitPrice'],
                                'original'          => $detail['original'] ?? false,
                                'original_number_h' => $detail['originalNumberH'] ?? 1,
                                'original_qty'      => $detail['originalDetailQty'] ?? 1,
                                'updated_at'        => Carbon::now(),
                            ];
                            if($detail['type'] == 'INTERIM'){
                                $detailData['interim_societe_id']   = $detail['societe'];
                            }
                            if($detail['type'] == 'COMMANDE FOURNISSEUR'){
                                $detailData['supplier_id']   = $detail['supplierId'];
                                $detailData['datesupplier']   = $detail['datesupplier'];
                                if(preg_match('/^data:application\/pdf;base64,/', $detail['base64'], $type)){
                                    $data = substr($detail['base64'], strpos($detail['base64'], ',') + 1);
                                    $type = 'pdf';
                                    if(!File::isDirectory(public_path('SupplierOrder/'))){
                                        File::makeDirectory(public_path('SupplierOrder/'), 0755, true, true);
                                    }
                                    $data = str_replace( ' ', '+', $data );
                                    $data = base64_decode($data);
                                    $uuid_filename = DB::select('select UUID() AS uuid')[0]->uuid;
                                    $orderFilePath = "/SupplierOrder/{$uuid_filename}.{$type}";
                                    Storage::disk('public')->put($orderFilePath, $data);
                                    $detailData['orderfile'] = $orderFilePath;
                                }
                            }
                            if(isset($detail['id']) && $detail['id'] != ''){
                                $detailId = $detail['id'];
                                DB::table('order_ouvrage_detail')->where('id', $detail['id'])->update($detailData);
                            }else{
                                $detailData['created_at'] = Carbon::now();
                                $detailId = DB::table('order_ouvrage_detail')->insertGetId($detailData);
                            }
                            $availTaskDetails[] = $detailId;
                        }
                        // set deleted_at field for deleted details
                        DB::table('order_ouvrage_detail')->where('order_ouvrage_task_id', $orderOuvrageTaskId)->where(function($query){
                            $query->whereNull('deleted_at')->orWhere('deleted_at', '0000-00-00 00:00:00');
                        })->whereNotIn('id', $availTaskDetails)->update(['deleted_at'=> Carbon::now()]);
                    }
                    // set deleted_at field for deleted tasks
                    DB::table('order_ouvrage_task')->where('order_ouvrage_id', $orderOuvrageId)->where(function($query){
                            $query->whereNull('deleted_at')->orWhere('deleted_at', '0000-00-00 00:00:00');
                        })->whereNotIn('id', $avileOuvrageTasks)->update(['deleted_at'=> Carbon::now()]);
                }
                // set deleted_at field for deleted installation ouvrages
                DB::table('order_ouvrages')->where('order_id', $orderId)->where(function($query){
                            $query->whereNull('deleted_at')->orWhere('deleted_at', '0000-00-00 00:00:00');
                        })->where('order_zone_id', $zoneId)->whereNotIn('id', $availInstallOuvrages)->where('type', 'INSTALLATION')->update(['deleted_at'=> Carbon::now()]);
            }
            if( count($zone['securityOuvrage']['ouvrages']) ){
                $orderCat = DB::table('order_cat')
                            ->where('order_zone_id', $zoneId)
                            ->where('type', 'SECURITE')
                            ->first();
                $securiteCat = [
                    'name'          =>  $zone['securityOuvrage']['name'],
                    'updated_at'    =>  Carbon::now(),
                ];
                if($orderCat){
                    $orderCatId = $orderCat->id;
                    DB::table('order_cat')->where('id', $orderCat->id)->update($securiteCat);
                }else{
                    $securiteCat['order_zone_id'] =  $zoneId;
                    $securiteCat['type']          =  'SECURITE';
                    $securiteCat['textcustomer']  =  '';
                    $securiteCat['textoperator']  =  '';
                    $securiteCat['created_at']    =  Carbon::now();
                    $orderCatId = DB::table('order_cat')->insertGetId($securiteCat);
                }
                // declare var for available installation ouvrage
                $availSecurityOuvrages = [];
                foreach ($zone['securityOuvrage']['ouvrages'] as $ouvrage) {
                    $ouvrageData = [
                        'order_id'          => $orderId,
                        'order_zone_id'     => $zoneId,
                        'order_cat_id'      => $orderCatId,
                        'unit_id'           => $ouvrage['unit'],
                        'qty'               => $ouvrage['qty'],
                        'nbheure'           => $ouvrage['totalHour'],
                        'total'             => $ouvrage['total'],
                        'ouvrage_prestation_id'=> $ouvrage['ouvrage_prestation_id'],
                        'ouvrage_toit_id'   => $ouvrage['ouvrage_toit_id'],
                        'ouvrage_metier_id' => $ouvrage['ouvrage_metier_id'],
                        'textcustomer'      => $ouvrage['customerText'],
                        'textchargeaffaire' => $ouvrage['textchargeaffaire'],
                        'textoperator'      => $ouvrage['textoperator'],
                        'name'              => $ouvrage['name'],
                        'updated_at'        => Carbon::now(),
                    ];
                    if(isset($ouvrage['id']) && $ouvrage['id'] != ''){
                        $orderOuvrageId = $ouvrage['id'];
                        DB::table('order_ouvrages')->where('id', $orderOuvrageId)->update($ouvrageData);
                    }else{
                        $ouvrageData['type']            = 'SECURITE';
                        $ouvrageData['created_at']      = Carbon::now();
                        $orderOuvrageId = DB::table('order_ouvrages')->insertGetId($ouvrageData);
                    }
                    // adding ouvrage's id;
                    $availSecurityOuvrages[] = $orderOuvrageId;
                    // declare var for available ouvrage tasks
                    $avileOuvrageTasks = [];
                    foreach ($ouvrage['tasks'] as $taskIndex => $task) {
                        $orderOuvrageTask = [
                            'order_ouvrage_id'      => $orderOuvrageId,
                            'name'                  => $task['name'],
                            'textcustomer'          => $task['customerText'],
                            'textchargeaffaire'     => $task['textchargeaffaire'],
                            'textoperator'          => $task['textoperator'],
                            'qty'                   => $task['qty'],
                            'unit_id'               => $task['unit_id'],
                            'updated_at'            =>  Carbon::now(),
                        ];
                        if(isset($task['id']) && $task['id'] != ''){
                            $orderOuvrageTaskId = $task['id'];
                            DB::table('order_ouvrage_task')->where('id', $task['id'])->update($orderOuvrageTask);
                        }else{
                            $orderOuvrageTask['created_at'] = Carbon::now();
                            $orderOuvrageTaskId = DB::table('order_ouvrage_task')->insertGetId($orderOuvrageTask);
                        }
                        // adding task's id;
                        $avileOuvrageTasks[] = $orderOuvrageTaskId;
                        $availTaskDetails = [];
                        foreach ($task['details'] as $detailIndex => $detail) {
                            $detailData = [
                                'order_ouvrage_task_id'   => $orderOuvrageTaskId,
                                'product_id'        => $detail['productId'],
                                'type'              => $detail['type'],
                                'name'              => $detail['name'],
                                'textcustomer'      => '',
                                'textchargeaffaire' => '',
                                'textoperator'      => '',
                                'qty'               => $detail['qty'],
                                'numberh'           => $detail['numberH'],
                                'unitprice'         => $detail['unitPrice'],
                                'qtyouvrage'        => $detail['qtyOuvrage'] == '' ? 0 : $detail['qtyOuvrage'],
                                'qtyunitouvrage'    => 1,
                                'houvrage'          => 0,
                                'marge'             => $detail['marge'],
                                'totalprice'        => $detail['totalPrice'],
                                'taxe_id'           => $detail['tax'],
                                'unit_id'           => $detail['unit_id'],
                                'priceachat'        => $detail['unitPrice'],
                                'original'          => $detail['original'] ?? false,
                                'original_number_h' => $detail['originalNumberH'] ?? 1,
                                'original_qty'      => $detail['originalDetailQty'] ?? 1,
                                'updated_at'        => Carbon::now(),
                            ];
                            if($detail['type'] == 'INTERIM'){
                                $detailData['interim_societe_id']   = $detail['societe'];
                            }
                            if($detail['type'] == 'COMMANDE FOURNISSEUR'){
                                $detailData['supplier_id']   = $detail['supplierId'];
                                $detailData['datesupplier']   = $detail['datesupplier'];
                                if(preg_match('/^data:application\/pdf;base64,/', $detail['base64'], $type)){
                                    $data = substr($detail['base64'], strpos($detail['base64'], ',') + 1);
                                    $type = 'pdf';
                                    if(!File::isDirectory(public_path('SupplierOrder/'))){
                                        File::makeDirectory(public_path('SupplierOrder/'), 0755, true, true);
                                    }
                                    $data = str_replace( ' ', '+', $data );
                                    $data = base64_decode($data);
                                    $uuid_filename = DB::select('select UUID() AS uuid')[0]->uuid;
                                    $orderFilePath = "/SupplierOrder/{$uuid_filename}.{$type}";
                                    Storage::disk('public')->put($orderFilePath, $data);
                                    $detailData['orderfile'] = $orderFilePath;
                                }
                            }
                            if(isset($detail['id']) && $detail['id'] != ''){
                                $detailId = $detail['id'];
                                DB::table('order_ouvrage_detail')->where('id', $detail['id'])->update($detailData);
                            }else{
                                $detailData['created_at'] = Carbon::now();
                                $detailId = DB::table('order_ouvrage_detail')->insertGetId($detailData);
                            }
                            $availTaskDetails[] = $detailId;
                        }
                        // set deleted_at field for deleted details
                        DB::table('order_ouvrage_detail')->where('order_ouvrage_task_id', $orderOuvrageTaskId)->where(function($query){
                            $query->whereNull('deleted_at')->orWhere('deleted_at', '0000-00-00 00:00:00');
                        })->whereNotIn('id', $availTaskDetails)->update(['deleted_at'=> Carbon::now()]);
                    }
                    // set deleted_at field for deleted tasks
                    DB::table('order_ouvrage_task')->where('order_ouvrage_id', $orderOuvrageId)->where(function($query){
                            $query->whereNull('deleted_at')->orWhere('deleted_at', '0000-00-00 00:00:00');
                        })->whereNotIn('id', $avileOuvrageTasks)->update(['deleted_at'=> Carbon::now()]);
                }
                // set deleted_at field for deleted security ouvrages
                DB::table('order_ouvrages')->where('order_id', $orderId)->where('order_zone_id', $zoneId)->where(function($query){
                            $query->whereNull('deleted_at')->orWhere('deleted_at', '0000-00-00 00:00:00');
                        })->whereNotIn('id', $availSecurityOuvrages)->where('type', 'SECURITE')->update(['deleted_at'=> Carbon::now()]);
            }
            if( count($zone['prestationOuvrage']['ouvrages']) ){

                $orderCat = DB::table('order_cat')
                            ->where('order_zone_id', $zoneId)
                            ->where('type', 'PRESTATION')
                            ->first();
                $prestationCat = [
                    'name'          =>  $zone['prestationOuvrage']['name'],
                    'updated_at'    =>  Carbon::now(),
                ];
                if($orderCat){
                    $orderCatId = $orderCat->id;
                    DB::table('order_cat')->where('id', $orderCat->id)->update($prestationCat);
                }else{
                    $prestationCat['order_zone_id'] =  $zoneId;
                    $prestationCat['type']          =  'PRESTATION';
                    $prestationCat['textcustomer']  =  '';
                    $prestationCat['textoperator']  =  '';
                    $prestationCat['created_at']    =  Carbon::now();
                    $orderCatId = DB::table('order_cat')->insertGetId($prestationCat);
                }
                // declare var for available installation ouvrage
                $availPrestationOuvrages = [];
                foreach ($zone['prestationOuvrage']['ouvrages'] as $ouvrage) {
                    $ouvrageData = [
                        'order_id'          => $orderId,
                        'order_zone_id'     => $zoneId,
                        'order_cat_id'      => $orderCatId,
                        'unit_id'           => $ouvrage['unit'],
                        'qty'               => $ouvrage['qty'],
                        'nbheure'           => $ouvrage['totalHour'],
                        'total'             => $ouvrage['total'],
                        'ouvrage_prestation_id'=> $ouvrage['ouvrage_prestation_id'],
                        'ouvrage_toit_id'   => $ouvrage['ouvrage_toit_id'],
                        'ouvrage_metier_id' => $ouvrage['ouvrage_metier_id'],
                        'textcustomer'      => $ouvrage['customerText'],
                        'textchargeaffaire' => $ouvrage['textchargeaffaire'],
                        'textoperator'      => $ouvrage['textoperator'],
                        'name'              => $ouvrage['name'],
                        'updated_at'        => Carbon::now(),
                    ];

                    if(isset($ouvrage['id']) && $ouvrage['id'] != ''){
                        $orderOuvrageId = $ouvrage['id'];
                        DB::table('order_ouvrages')->where('id', $orderOuvrageId)->update($ouvrageData);
                    }else{
                        $ouvrageData['type']            = 'PRESTATION';
                        $ouvrageData['created_at']      = Carbon::now();
                        $orderOuvrageId = DB::table('order_ouvrages')->insertGetId($ouvrageData);
                    }
                    // adding ouvrage's id;
                    $availPrestationOuvrages[] = $orderOuvrageId;
                    // declare var for available ouvrage tasks
                    $avileOuvrageTasks = [];
                    foreach ($ouvrage['tasks'] as $taskIndex => $task) {
                        $orderOuvrageTask = [
                            'order_ouvrage_id'      => $orderOuvrageId,
                            'name'                  => $task['name'],
                            'textcustomer'          => $task['customerText'],
                            'textchargeaffaire'     => $task['textchargeaffaire'],
                            'textoperator'          => $task['textoperator'],
                            'qty'                   => $task['qty'],
                            'unit_id'               => $task['unit_id'],
                            'updated_at'            =>  Carbon::now(),
                        ];
                        if(isset($task['id']) && $task['id'] != ''){
                            $orderOuvrageTaskId = $task['id'];
                            DB::table('order_ouvrage_task')->where('id', $task['id'])->update($orderOuvrageTask);
                        }else{
                            $orderOuvrageTask['created_at'] = Carbon::now();
                            $orderOuvrageTaskId = DB::table('order_ouvrage_task')->insertGetId($orderOuvrageTask);
                        }
                        // adding task's id;
                        $avileOuvrageTasks[] = $orderOuvrageTaskId;
                        $availTaskDetails = [];
                        foreach ($task['details'] as $detailIndex => $detail) {
                            $detailData = [
                                'order_ouvrage_task_id'   => $orderOuvrageTaskId,
                                'product_id'        => $detail['productId'],
                                'type'              => $detail['type'],
                                'name'              => $detail['name'],
                                'textcustomer'      => '',
                                'textchargeaffaire' => '',
                                'textoperator'      => '',
                                'qty'               => $detail['qty'],
                                'numberh'           => $detail['numberH'],
                                'unitprice'         => $detail['unitPrice'],
                                'qtyouvrage'        => $detail['qtyOuvrage'] == '' ? 0 : $detail['qtyOuvrage'],
                                'qtyunitouvrage'    => 1,
                                'houvrage'          => 0,
                                'marge'             => $detail['marge'],
                                'totalprice'        => $detail['totalPrice'],
                                'taxe_id'           => $detail['tax'],
                                'unit_id'           => $detail['unit_id'],
                                'priceachat'        => $detail['unitPrice'],
                                'original'          => $detail['original'] ?? false,
                                'original_number_h' => $detail['originalNumberH'] ?? 1,
                                'original_qty'      => $detail['originalDetailQty'] ?? 1,
                                'updated_at'        => Carbon::now(),
                            ];
                            if($detail['type'] == 'INTERIM'){
                                $detailData['interim_societe_id']   = $detail['societe'];
                            }
                            if($detail['type'] == 'COMMANDE FOURNISSEUR'){
                                $detailData['supplier_id']   = $detail['supplierId'];
                                $detailData['datesupplier']   = $detail['datesupplier'];
                                if(preg_match('/^data:application\/pdf;base64,/', $detail['base64'], $type)){
                                    $data = substr($detail['base64'], strpos($detail['base64'], ',') + 1);
                                    $type = 'pdf';
                                    if(!File::isDirectory(public_path('SupplierOrder/'))){
                                        File::makeDirectory(public_path('SupplierOrder/'), 0755, true, true);
                                    }
                                    $data = str_replace( ' ', '+', $data );
                                    $data = base64_decode($data);
                                    $uuid_filename = DB::select('select UUID() AS uuid')[0]->uuid;
                                    $orderFilePath = "/SupplierOrder/{$uuid_filename}.{$type}";
                                    Storage::disk('public')->put($orderFilePath, $data);
                                    $detailData['orderfile'] = $orderFilePath;
                                }
                            }
                            if(isset($detail['id']) && $detail['id'] != ''){
                                $detailId = $detail['id'];
                                DB::table('order_ouvrage_detail')->where('id', $detail['id'])->update($detailData);
                            }else{
                                $detailData['created_at'] = Carbon::now();
                                $detailId = DB::table('order_ouvrage_detail')->insertGetId($detailData);
                            }
                            $availTaskDetails[] = $detailId;
                        }
                        // set deleted_at field for deleted details
                        DB::table('order_ouvrage_detail')->where('order_ouvrage_task_id', $orderOuvrageTaskId)->where(function($query){
                            $query->whereNull('deleted_at')->orWhere('deleted_at', '0000-00-00 00:00:00');
                        })->whereNotIn('id', $availTaskDetails)->update(['deleted_at'=> Carbon::now()]);
                    }
                    // set deleted_at field for deleted tasks
                    DB::table('order_ouvrage_task')->where('order_ouvrage_id', $orderOuvrageId)->where(function($query){
                            $query->whereNull('deleted_at')->orWhere('deleted_at', '0000-00-00 00:00:00');
                        })->whereNotIn('id', $avileOuvrageTasks)->update(['deleted_at'=> Carbon::now()]);
                }
                // set deleted_at field for deleted security ouvrages
                DB::table('order_ouvrages')->where('order_id', $orderId)->where('order_zone_id', $zoneId)->where(function($query){
                            $query->whereNull('deleted_at')->orWhere('deleted_at', '0000-00-00 00:00:00');
                        })->whereNotIn('id', $availPrestationOuvrages)->where('type', 'PRESTATION')->update(['deleted_at'=> Carbon::now()]);
            }
        }
        // label deleted_at for deleted zones
        DB::table('order_zones')->where('order_id', $orderId)->where(function($query){
                            $query->whereNull('deleted_at')->orWhere('deleted_at', '0000-00-00 00:00:00');
                        })->whereNotIn('id', $availOrderZoneIds)->update(['deleted_at'=> Carbon::now()]);
        return response()->json(['success'=> true]);
    }
}
