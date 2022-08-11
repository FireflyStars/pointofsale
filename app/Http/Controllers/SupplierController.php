<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SupplierController extends Controller
{
    /**
     * Create a supplier
     */
    public function store(Request $request){
        $supplier['affilie_id'] = auth()->user()->affiliate_id;
        $supplier['supplier_type_id'] = $request->type;
        $supplier['supplier_status_id'] = $request->status;
        $supplier['name'] = $request->nom;
        $supplier['naf'] = $request->naf;
        $supplier['actif'] = $request->active;
        $supplier['contactname'] = $request->contact;
        $supplier['phone'] = $request->phoneNumber !='' ? $request->phoneCode.'|'.$request->phoneNumber : '';
        $supplier['email'] = $request->email;
        $supplier['comment'] = $request->comment;

        $supplierId = DB::table('supplier')->insertGetId($supplier);
        return response()->json([
            'success'   => true,
            'id'        => $supplierId,
        ]);
    }

    /**
     * Get a supplier
     */
    public function edit(Request $request, $supplierId){
        $supplier = DB::table('supplier')->where('id', $supplierId)->first();
        return response()->json(
            $supplier
        );
    }

    /**
     * Update a supplier
     */
    public function update(Request $request, $supplierId){
        $supplier = [];
        DB::table('supplier')->where('id', $supplierId)->update($supplier);
        return response()->json([
            'success'   => true
        ]);
    }
}
