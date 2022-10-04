<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Supplier;

class FournisseurSearchController extends Controller
{

   public function index(Request $request) 
   {
         
      $suppliers = Supplier::query();

      $search = $request->search;
      
      $suppliers = $suppliers
                  ->select( 
                     'suppliers.id',
                     'suppliers.name',
                     'suppliers.email',
                     'suppliers.phone',
                     'suppliers.contactname',
                     'supplier_orders.total',
                     'suppliers.created_at',
                  )
                  ->leftJoin('supplier_orders', 'supplier_orders.supplier_id', '=', 'suppliers.id')
                  ->where(function($query) use ($search) {
                     $query->where('suppliers.name', 'like', '%' . $search . '%')
                     ->orWhere('suppliers.email', 'like', '%' . $search . '%')
                     ->orWhere('suppliers.contactname', 'like', '%' . $search . '%')
                     ->orWhere('suppliers.phone', 'like', '%' . $search . '%');
                  });

      return response()->json([
         'data'  => $request->has('showmore') ? $suppliers->get() : $suppliers->take(5)->get(),
         'total' => $suppliers->count()
      ]);

   }

}
