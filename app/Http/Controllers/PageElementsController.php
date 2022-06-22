<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\page_builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use App\Http\Resources\reportsResource;

class PageElementsController extends Controller
{

    public function store(Request $request) 
    {

        $pages = json_decode($request->pages);

        $pdf = App::make('dompdf.wrapper');

        $pdf->setOptions([
            'enable_php'           => true,
            'isRemoteEnabled'      => true, 
            'isHtml5ParserEnabled' => true, 
        ]);

        $pdf->loadView(
            'page-multiple', [
                'pages'     => $pages,
                'svgs'      => page_builder::get_svgs(),
                'builder'   => (new page_builder),
                'affiliate' => $this->get_order_affiliate($request)
            ]
        );

        return $pdf->download('Page.pdf');
        
    }

    public function get_order_affiliate(Request $request) 
    {
        return $request->has('order_id') && !is_null($request->order_id) 
        ? optional(Order::find($request->order_id))->affiliate 
        : null;
    }

    public function get_page_order(Order $order) 
    {
        return response()->json(
            new reportsResource($order)
        );
    }

    public function get_page_templates() 
    {
        return response()->json(page_builder::templates());   
    }


}
