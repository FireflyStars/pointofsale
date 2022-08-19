<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SupplierOrder;
use App\Models\SupplierOrderState;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\TableFiltersController;

class CommandeFounisseurController extends Controller
{
    
    private function get_data(Request $request) 
    {
        
        $orders = SupplierOrder::query();
        $orders = $orders->select(
            'supplier_orders.id',
            'suppliers.name',
            'supplier_orders.user_id',
            'reference',
            'supplier_order_state_id',
            'supplier_orders.total as montant',
            DB::raw(
                'TRIM(
                    CONCAT(
                        suppliers.contactname,  
                        "<br>",
                        suppliers.phone,
                        "<br>",
                        suppliers.email
                    )
                ) 
            as contact'),
            DB::raw('DATE_FORMAT(supplier_orders.dateinvoice, "%Y-%m-%d") as dateinvoice'),            
            DB::raw('DATE_FORMAT(supplier_orders.created_at, "%Y-%m-%d") as created_at'),            
        );

        $orders = $orders->leftJoin('supplier_order_state', 'supplier_order_state.id', '=', 'supplier_order_state_id')
        ->leftJoin('suppliers', 'suppliers.id', '=', 'supplier_orders.supplier_id');

        $data = (new TableFiltersController)->sorts($request, $orders, 'supplier_orders.id');
        $data = (new TableFiltersController)->filters($request, $data);

        $data = $data->take($request->take ?? 15)
        ->skip($request->skip ?? 0);

        return $data;

    }

    public function index(Request $request) 
    {

        return response()->json(
            $this->get_data($request)
            ->get()
        );

    }

    public function mes_details(Request $request) 
    {

        return response()->json(
            $this->get_data($request)
            ->where('affiliate_id', $request->user()->affiliate_id)
            ->get()
        );

    }

    public function get_order_states() 
    {
        return response()->json(
            SupplierOrderState::select('id', 'name as value')
                        ->get()       
        );
    }

    public function get_order_states_all() 
    {
        return response()->json(
            SupplierOrderState::all()    
        );
    }

}
