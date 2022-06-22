<?php

namespace App\Http\Controllers;

use App\Models\Template;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Traits\TemplateFormattedFiles;
use App\Http\Resources\TemplateResource;
use App\Http\Controllers\TableFiltersController;

class TemplatesController extends Controller
{
    use TemplateFormattedFiles;

    public function report_templates() 
    {
        return response()->json(Template::all());
    }
    
    public function index(Request $request) 
    {

        $data = Template::where('templates.affiliate_id', $request->user()->id)
                ->join('users', 'users.id', '=', 'templates.affiliate_id')
                ->select(
                    'templates.id',
                    'templates.name',
                    'users.name as affiliate',
                    'pages',
                    'page_files',
                    DB::raw('DATE_FORMAT(templates.created_at, "%Y-%m-%d") as created_at'),
                );

        $data = (new TableFiltersController)->sorts($request, $data, 'templates.id');

        $data = (new TableFiltersController)->filters($request, $data);
        
        $data = $data
                ->skip($request->skip ?? 0)
                ->take($request->take ?? 15)
                ->get();

        return response()->json($data);
        
    }
    
    public function store(Request $request) 
    {
        $page_files = $this->get_page_files($request);
        $pages = gettype($request->pages) == 'string' ? json_decode($request->pages, true) : $request->pages;

        Template::create([
            'name'         => $request->name, 
            'affiliate_id' => Auth::user()->id,
            'pages'        => $pages,
            'page_files'   => $page_files,
        ]);  
        
        return response()->json('Template saved', 201);    

    }

    public function show(Template $template) 
    {
        return response()->json(
            new TemplateResource($template)
        );
    }

    public function update(Template $template, Request $request) 
    {
        
        $pages = gettype($request->pages) == 'string' ? json_decode($request->pages, true) : $request->pages;
        $page_files = $this->get_page_files($request);

        $template->update([
            'pages'      => $pages,
            'page_files' => $page_files 
        ]);

        return response()->json('Template saved', 200); 

    }


}
