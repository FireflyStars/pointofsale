<?php 

namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

trait TemplateFormattedFiles 
{

    public function get_formatted_files($files) 
    {
        return array_map(function($file, $key) {
            return [
                'file'         => $file['file'],
                'page'         => $file['page'],
                'storage_path' => public_path('\/storage/' . $file['file']),
                'public_path'  => $this->get_file_public_path($file, $key),
                'base_path'    => config('app.url'),
                'id'           => $key 
            ];
        }, $files, array_keys($files));
    }

    private function get_file_public_path($file, $key) 
    {
        
        $app_url = rtrim(config('app.url'), '/');

        if($key == 'backgroundImage_' . $file['page']) 
        {
            
            $filename = ltrim($file['file'], '\/');

            if(strpos($filename, 'report-templates') !== false) 
            {
                return $app_url . Storage::url($file['file']);
            }

            return $app_url . '/' . $filename;

        }
        else 
        {
            return $app_url . Storage::url($file['file']);
        }
    }

    public function get_page_files(Request $request) 
    {
        
        $page_files = [];
        $pages = gettype($request->pages) == 'string' ? json_decode($request->pages, true) : $request->pages;
        
        if(count($pages)) 
        {
            $page_count = 0;
            foreach($pages as $page) 
            {

                if(!is_null($page['background']) && count($page['background'])) 
                {
                    $background = $page['background'];
                    if($background['prefetched'] == true) 
                    {
                        $src = $background['dataFile'];
                    }
                    else 
                    {
                        $src = $request->file('BackgroundImage#' . $background['attributes']['id'])
                                ->store('report-templates', 'public');
                    }
                    $page_files['backgroundImage_' . $page_count] = [
                        'file'         => $src,
                        'storage_path' => '',
                        'page'   => $page_count 
                    ];
                }


                foreach($page['elements'] as $element) 
                {
                    if($element['name'] == 'img') 
                    {
                        
                        if($element['prefetched'] == true) 
                        {
                            $src = $element['dataFile'];
                        }
                        else 
                        {
                            $src = $request->file('Img#' . $element['attributes']['id'])
                                    ->store('report-templates', 'public');
                        }
                        
                        $page_files[$element['attributes']['id']] = [
                            'file'         => $src,
                            'storage_path' => '',
                            'page'         => $page_count
                        ];    
                    }
                }
                $page_count++;
            }
        }

        return $page_files;
    
    }

}