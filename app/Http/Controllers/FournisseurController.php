<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Models\SupplierType;
use Illuminate\Http\Request;
use App\Models\SupplierOrder;
use App\Models\SupplierStatus;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\TableFiltersController;
use App\Http\Resources\SupplierResource;

class FournisseurController extends Controller
{

    private function get_data(Request $request) 
    {
        
        $orders = SupplierOrder::query()
                ->select(
                    'supplier_orders.supplier_id',
                    DB::raw('SUM(supplier_orders.total) as montant'),
                    DB::raw('COUNT(supplier_orders.id) as count_orders'),
                )
                ->groupBy('supplier_orders.supplier_id');
        
        $suppliers = Supplier::query();

        $suppliers = $suppliers->select(
            'suppliers.id',
            'suppliers.name',
            'suppliers.email',
            'suppliers.comment',
            'supplier_type.name as supplier_type',
            'supplier_status_id',   
            'suppliers.actif',
            DB::raw('IFNULL(orders.montant, 0) as montant'),
            DB::raw('IFNULL(orders.count_orders, 0) as count_orders'),
            DB::raw(
                'TRIM(
                    CONCAT(
                        suppliers.contactname,  
                        "<br>",
                        suppliers.phone
                    )
                ) 
            as contact'),
            DB::raw('DATE_FORMAT(suppliers.created_at, "%Y-%m-%d") as created_at'),            
        );

        $suppliers = $suppliers->leftJoin('supplier_type', 'supplier_type.id', '=', 'supplier_type_id')
        ->leftJoinSub($orders, 'orders', function($join) {
            $join->on('orders.supplier_id', '=', 'suppliers.id');
        });

        $data = (new TableFiltersController)->sorts($request, $suppliers, 'suppliers.id');
        $data = (new TableFiltersController)->filters($request, $data);

        $data = $data->take($request->take ?? 15)
        ->skip($request->skip ?? 0);

        return $data;

    }
    
    public function index(Request $request) 
    {

        $data = $this->get_data($request)->get();            

        return response()->json($data);   

    }

    public function fournisseur_mes(Request $request) 
    {

        $data = $this->get_data($request)
        ->where('affilie_id', $request->user()->affiliate_id)
        ->get();            

        return response()->json($data);    

    }

    public function fournisseur_types() 
    {
        return response()->json(
            SupplierType::select('id', 'name as value')
            ->get()
        );
    }

    public function fournisseur_status() 
    {
        return response()->json(
            SupplierStatus::select('id', 'name as value')
            ->get()
        );
    }

    public function fournisseur_status_all() 
    {
        return response()->json(
            SupplierStatus::all()
        );
    }

    public function fournisseur_details(Supplier $supplier) 
    {
        return response()->json(
            new SupplierResource($supplier)
        );
    }

    public function fournisseur_history(Supplier $supplier) 
    {
        return response()->json(
            $supplier
        );
    }


}
