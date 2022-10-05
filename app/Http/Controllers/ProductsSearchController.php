<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductsSearchController extends Controller
{
    
    public function index(Request $request) 
    {

        $products = Product::query();

        $search = $request->search;
        
        $products = $products
                    ->select( 
                        'products.id',
                        'products.name',
                        'products.reference',
                        'products.supplier_reference',
                        'products.type',
                        'products.description',
                        'products.price'
                    )
                    ->where(function($query) use ($search) {
                        $query->where('products.name', 'like', '%' . $search . '%')
                        ->orWhere('products.reference', 'like', '%' . $search . '%')
                        ->orWhere('products.type', 'like', '%' . $search . '%')
                        ->orWhere('products.description', 'like', '%' . $search . '%');
                    });

        return response()->json([
            'data'  => $request->has('showmore') ? $products->get() : $products->take(5)->get(),
            'total' => $products->count()
        ]);

    }

    public function product_units() 
    {
        return response()->json(
            Unit::select('id as value', 'name as display')
            ->get()
        );
    }

}
