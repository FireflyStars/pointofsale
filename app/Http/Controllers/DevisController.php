<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderState;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DevisController extends Controller
{
    //

    public function loadList(Request $request){

        $column_filters=$request->post('column_filters');
        $column_sortby=$request->post('sortby');
        $skip=$request->post('skip');
        $take=$request->post('take');
  

        $orderList=DB::table('orders')
        ->select(['orders.*',
        'events.id as evnt',
        'users.name as responsable', 
        DB::raw("CONCAT(customers.company,' ',customers.firstname,' ',customers.name) as customer"),
        DB::raw("CONCAT(contacts.firstname,' ',contacts.name,'<br/>',contacts.mobile) as contact"),
        DB::raw("CONCAT(addresses.address1, IF(addresses.address2<>'', '<br/>',''),addresses.address2,'<br/>',addresses.postcode,' ',addresses.city) as address")])
          ->join('customers',function($join){
            $join->on('customers.id','=','orders.customer_id');
        })->join('events',function($join){
            $join->on('events.order_id','=','orders.id');
        })->join('contacts',function($join){
            $join->on('contacts.id','=','events.contact_id');
        })->leftJoin('addresses',function($join){
            $join->on('addresses.id','=','events.address_id')
            ->where('addresses.address_type_id','=',2);
        })->leftJoin('users',function($join){
            $join->on('users.id','=','orders.responsable_id');
        });

        //column filters
        if($column_filters!=null)
        foreach($column_filters as $column_filter){
            if(isset($column_filter['having'])&&$column_filter['having']==true){
                $orderList=$orderList->having($column_filter['id'],'LIKE','%'.$column_filter['word'].'%');
            }else{
                $orderList=$orderList->where((isset($column_filter['table'])?$column_filter['table'].'.':'').$column_filter['id'],'LIKE','%'.$column_filter['word'].'%');
            }
        }

        //sortby
        if($column_sortby!=null)
        foreach($column_sortby as $sortby){
            $orderList=$orderList->orderBy($sortby['id'],$sortby['orderby']);
        }
        

        $orderList=$orderList->groupBy('orders.id')->skip($skip)->take($take)->get();
        return response()->json($orderList);
    }

    public function getOrderStates(Request $request){
        return response()->json(OrderState::all());
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
        return response()->json($categories->groupBy('id'));
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
                        'unit_id as unit', 'textcustomer as customerText', 'name', 'id'
                        )
                    ->where('id', $request->id)->first();
        $ouvrage->qty = 0;
        $ouvrage->totalHour = 0;
        $ouvrage->avg = 0;
        $ouvrage->qtyOuvrage = $request->qtyOuvrage;
        $ouvrage->total = 0;
        $ouvrage->totalWithoutMarge = 0;
        $tasks = DB::table('ouvrage_task')
                ->where('ouvrage_id', $request->id)
                ->select('id', 'name', 'textcustomer as customerText', 'textchargeaffaire', 'unit_id', 'qty')
                ->get();
        $ouvrage->tasks = $tasks;
        foreach ($tasks as $task) {
            $details = DB::table('ouvrage_detail')
                            ->join('products', 'products.id', '=', 'ouvrage_detail.product_id')
                            ->join('units', 'units.id', '=', 'products.unit_id')
                            ->where('ouvrage_task_id', $task->id)
                            ->select(
                                'ouvrage_detail.id', 'ouvrage_detail.numberh as numberH', 'ouvrage_detail.qty', 'units.id as unit_id',
                                'products.type', 'units.code as unit'
                            )->get();
            foreach ($details as $detail) {
                $detail->unitPrice = 0;
                $detail->marge = 0;
                $detail->totalPrice = 0;
                $detail->qtyOuvrage = (int)$request->qtyOuvrage*((int)$detail->qty);
                $detail->totalPriceWithoutMarge = 0;
                $detail->tax = 0;
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
            $query =    $query->where('name', 'like', '%'.$request->search.'%')
                        ->orWhere('codelcdt', 'like', '%'.$request->search.'%')
                        ->orWhere('textchargeaffaire', 'like', '%'.$request->search.'%');
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
                ->join('units', 'units.id', '=', 'products.unit_id');
        if($request->search != ''){
            $query =    $query->where('products.name', 'like', '%'.$request->search.'%')
                        ->orWhere('products.reference', 'like', '%'.$request->search.'%')
                        ->orWhere('products.description', 'like', '%'.$request->search.'%');
        }
        if($request->type != '')
            $query =    $query->where('products.type', $request->type);
        return response()->json(
            $query->select(
                'products.id', 'products.name',
                'products.description', 'products.type', 'products.unit_id', DB::raw('CEIL(taxes.taux * 100) as tax'),
                'products.reference', 'products.wholesale_price', 'products.type', 'units.code as unit'
            )->get()
        );
    }
}
