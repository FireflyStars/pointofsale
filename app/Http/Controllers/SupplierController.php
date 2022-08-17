<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SupplierController extends Controller
{
    /**
     * Create a supplier
     */
    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'naf'               => 'required',
            'nom'               => 'required',
            'siret'             => 'required|unique:suppliers,siret',
            'email'             => $request->email != '' ? 'email' : '',
        ]);        
        if($validator->fails()){
            return response()->json([
                'success'=> false,
                'errors'  =>$validator->errors()
            ]);
        }else{
            $supplier['affilie_id'] = auth()->user()->affiliate_id;
            $supplier['supplier_type_id'] = $request->type;
            $supplier['supplier_status_id'] = $request->status;
            $supplier['name'] = $request->nom;
            $supplier['siret'] = $request->siret;
            $supplier['naf'] = $request->naf;
            $supplier['actif'] = $request->active;
            $supplier['contactname'] = $request->contact;
            $supplier['phone'] = $request->phoneNumber !='' ? $request->phoneCode.'|'.$request->phoneNumber : '';
            $supplier['email'] = $request->email;
            $supplier['comment'] = $request->comment;
            $supplier['created_at'] = now();
            $supplier['updated_at'] = now();
    
            $supplierId = DB::table('suppliers')->insertGetId($supplier);
            return response()->json([
                'success'   => true,
                'id'        => $supplierId,
            ]);
        }
    }

    /**
     * Get a supplier
     */
    public function edit(Request $request, $supplierId){
        $supplier = DB::table('suppliers')
                    ->where('id', $supplierId)
                    ->select( 'id', 'supplier_type_id as type', 'supplier_status_id as status', 'contactname as contact',
                    'name as nom', 'actif as active', 'phone as phoneNumber', 'email', 'comment', 'naf', 'siret',
                    DB::raw('DATE_FORMAT(created_at, "%m/%d/%Y") as created'),
                    DB::raw('DATE_FORMAT(updated_at, "%m/%d/%Y") as updated'),
                     )
                    ->first();
        return response()->json([
            'supplier'=>$supplier,
            'status' => DB::table('supplier_status')->select('name as display', 'id as value')->get(),
            'type'   => DB::table('supplier_type')->select('name as display', 'id as value')->get(),
            'nafs'   => DB::table('customer_naf')->select('code', 'name')->orderBy('name')->get()            
        ]);
    }

    /**
     * Update a supplier
     */
    public function update(Request $request, $supplierId){
        $validator = Validator::make($request->all(), [
            'naf'               => 'required',
            'nom'               => 'required',
            'siret'             => 'required|unique:suppliers,siret,'.$supplierId,
            'email'             => $request->email != '' ? ('email|unique:suppliers,email,'.$supplierId) : '',
        ]);        
        if($validator->fails()){
            return response()->json([
                'success'=> false,
                'errors'  =>$validator->errors()
            ]);
        }else{        
            $supplier['affilie_id'] = auth()->user()->affiliate_id;
            $supplier['supplier_type_id'] = $request->type;
            $supplier['supplier_status_id'] = $request->status;
            $supplier['name'] = $request->nom;
            $supplier['siret'] = $request->siret;
            $supplier['naf'] = $request->naf;
            $supplier['actif'] = $request->active;
            $supplier['contactname'] = $request->contact;
            $supplier['phone'] = $request->phoneNumber !='' ? $request->phoneCode.'|'.$request->phoneNumber : '';
            $supplier['email'] = $request->email;
            $supplier['comment'] = $request->comment;
            DB::table('suppliers')->where('id', $supplierId)->update($supplier);
            return response()->json([
                'success'   => true
            ]);
        }
    }

    /**
     * Get status, type
     * 
     */
    public function getSupplierStatusType(Request $request){
        return response()->json([
            'status' => DB::table('supplier_status')->select('name as display', 'id as value')->get(),
            'type'   => DB::table('supplier_type')->select('name as display', 'id as value')->get(),
            'nafs'   => DB::table('customer_naf')->select('code', 'name')->orderBy('name')->get()
        ]);
    }
}
