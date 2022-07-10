<?php

namespace App\Http\Resources;

use App\Traits\TemplateFormattedFiles;
use Illuminate\Http\Resources\Json\JsonResource;

class ReportsCollectionResource extends JsonResource
{
    
    use TemplateFormattedFiles;

    public function toArray($request)
    {
        return [
            'id'            => $this->id,
            'name'          => $this->name,
            'template_name' => optional($this->template)->name,
            'affiliate'     => optional($this->affiliate)->name,
            'pages'         => count($this->pages) ? $this->pages : [],
            'page_files'    => count($this->page_files) 
                                ? $this->get_formatted_files($this->page_files)
                                : [],
            'created_at'    => $this->created_at->format('Y-m-d'),
        ];
    }

}
