<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductsController extends Controller
{
    
    public function store(Request $request) 
    {
        
        $product = Product::create([
            'affilie_id' => $request->user()->affiliate_id,
            'unit_id' => $request->unit,
            'name' => $request->name,
            'price' => $request->price,
            'active' => 1,
            'description' => $request->description,
            'type' => $request->type,
        ]);

        return response()->json($product, 201);

    }

}
