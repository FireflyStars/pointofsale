<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Report;
use App\Models\page_builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use App\Http\Resources\reportsResource;

class PageElementsController extends Controller
{

    public function delete_report(Report $report) 
    {
        $report->delete();
        return response()->json("Report deleted");
    }

    public function generate_pdf_by_id(Report $report) 
    {
        
        $pages = json_encode($report->pages);

        $pdf = App::make('dompdf.wrapper');

        $pdf->setOptions([
            'enable_php'           => true,
            'isRemoteEnabled'      => true, 
            'isHtml5ParserEnabled' => true, 
        ]);

        $pdf->loadView(
            'report-page-multiple', [
                'pages'     => json_decode($pages),
                'page_files'=> $report->page_files,
                'svgs'      => page_builder::get_svgs(),
                'builder'   => (new page_builder),
                'affiliate' => $report->affiliate
            ]
        );

        return $pdf->download('Report.pdf');

    }

    public function generate_pdf(Request $request) 
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
