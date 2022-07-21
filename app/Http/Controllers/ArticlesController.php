<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\ArticlesResource;
use App\Http\Controllers\TableFiltersController;
use App\Models\ProductDocument;
use App\Models\ProductDocumentType;

class ArticlesController extends Controller
{
    
    public function __construct() 
    {
        return $this->middleware(['auth']);
    }

    public function index(Request $request) 
    {

        $details = Product::query();

        $details->select(
            'products.id',
            'products.name',
            'products.description',
            'products.reference',
            'products.supplier_reference',
            'products.type',
            'units.code as unite',
            DB::raw('FORMAT(products.price, 2) as product_price'),
            DB::raw('FORMAT(products.wholesale_price, 2) as product_wholesale_price'),
            DB::raw('DATE_FORMAT(products.created_at, "%Y-%m-%d") as created_at'),
        )->leftJoin('units', 'units.id', '=', 'products.unit_id');

        $details = (new TableFiltersController)->sorts($request, $details, 'products.id');
        $details = (new TableFiltersController)->filters($request, $details);

        $details = $details
        ->where('products.affilie_id', $request->user()->affiliate_id)
        ->take($request->take ?? 15)
        ->skip($request->skip ?? 0)
        ->get();


        return response()->json(
            $details
        );

    }


    public function show(Product $product) 
    {
        
        return response()->json(
            new ArticlesResource($product)
        );

    }

    public function valider(Product $product, Request $request) 
    {
        
        if($product->productAffiliate()->where('affilie_id', $request->user()->affiliate_id)->count()) 
        {
            
            $product->productAffiliate()->update([
                'wholesale_price' => $request->wholesale_price
            ]); 

           return response()->noContent();

        }

        $product->productAffiliate()->create([
            'affilie_id'         => $request->user()->affiliate_id,
            'reference'          => $product->reference,
            'supplier_reference' => $product->supplier_reference,
            'weight'             => $product->weight,
            'active'             => $product->active,
            'ean13'              => $product->ean13,
            'description'        => $product->description,
            'wholesale_price'    => $request->wholesale_price,
            'supplier_id'        => $product->supplier_id,
            'taxe_id'            => $product->taxe_id,
            'unit_id'            => $product->unit_id,
            'type'               => $product->type,
        ]);

        return response()->noContent();

    }

    public function load_product_documents(Product $product, Request $request)
    {

        $product_documents = array();

        $documents = $product->documents()
                    ->latest('created_at')
                    ->when($request->has('take') && $request->take != null, function($query) use ($request) {
                        $query->take($request->take ?? 3);
                    })
                    ->get();

        foreach($documents as $document)
        {
            $document->type = $document->type;
            $document->product = $document->product;
            $product_documents[] = $document;
        }

        return response()->json($product_documents); 

    }

    public function upload_product_document(Request $request)
    {

        $request->validate([
            'files'        => 'required|mimetypes:application/pdf',
            'name'         => 'required',
        ]);

        $file = $request->file('files');

        $document = new ProductDocument;
        
        $document->affiliate_id = $request->user()->affiliate_id;
        $document->product_id = $request->productId;
        $document->human_readable_filename = $file->getClientOriginalName();
        $document->name = $request->name;
        $document->product_documents_type_id = $request->type;
        $uuid_filename = DB::select('select UUID() AS uuid')[0]->uuid;
        $filename = $uuid_filename . '.' . $file->getClientOriginalExtension();
        $document->file_path = $file->storeAs('PRODUCTS/PRODUCT' . $document->affiliate_id . '/product_documents', $filename, 'public');
        $document->save();

        return response()->noContent();
      
    }

    public function product_document_types() 
    {
        
        return response()->json(
            ProductDocumentType::all()
        );

    }

    public function get_document_url(ProductDocument $document) 
    {
        return response()->json(
            array('document_url' => route('downloadPdfFile') . '?path=' . $document->file_path . '&filename=' . $document->human_readable_filename)
        );
    }

    public function remove_product_document(ProductDocument $document)
    {

        $document->delete();

    }



}
