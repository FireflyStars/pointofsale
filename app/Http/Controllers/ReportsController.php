<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Order;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Traits\TemplateFormattedFiles;
use App\Http\Controllers\TableFiltersController;
use App\Http\Resources\ReportsCollectionResource;

class ReportsController extends Controller
{
    use TemplateFormattedFiles;
    
    public function index(Request $request) 
    {

        $affiliate_id = $request->user()->affiliate_id;

        $reports = Report::query()->where('reports.affiliate_id', $affiliate_id)
        ->leftJoin('templates', 'templates.id', '=', 'reports.template_id')
        ->leftJoin('affiliates', 'affiliates.id', '=', 'reports.affiliate_id')
        ->select(
            'reports.id',
            'order_id', 
            'templates.name as template_name',
            'reports.id as report_id',
            'affiliates.name as affiliate',
            DB::raw('reports.pages as pages'),
            DB::raw('DATE_FORMAT(reports.created_at, "%Y-%m-%d") as created_at')
        );

        $reports = (new TableFiltersController)->sorts($request, $reports, 'reports.id');
        $reports = (new TableFiltersController)->filters($request, $reports);
        
        $reports = $reports
                ->skip($request->skip ?? 0)
                ->take($request->take ?? 15);

        return response()->json(
            $reports->get()
        );
        
    }

    public function show(Report $report) 
    {
        return response()->json(
            new ReportsCollectionResource($report)
        );
    }
    
    public function store(Request $request) 
    {

        $page_files = $this->get_page_files($request);
        $pages = gettype($request->pages) == 'string' ? json_decode($request->pages, true) : $request->pages;

        $order = Order::find($request->order_id);

        $report = $order->reports()->create([
            'name'         => $request->name,
            'template_id'  => $request->template_id, 
            'affiliate_id' => optional(optional(Auth::user())->affiliate)->id,
            'user_id'      => optional(Auth::user())->id,
            'pages'        => $pages,
            'page_files'   => $page_files,
        ]);  
        
        return response()->json($report, 201);    

    }

    public function update(Report $report, Request $request) 
    {
        
        $pages = gettype($request->pages) == 'string' ? json_decode($request->pages, true) : $request->pages;
        $page_files = $this->get_page_files($request);

        $report->update([
            'pages'      => $pages,
            'page_files' => $page_files 
        ]);

        return response()->json($report, 201); 

    }


    public function delete(Report $report) 
    {
        $report->delete();
        return response()->noContent();
    }

}
