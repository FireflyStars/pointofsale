<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class page_builder extends Model
{
    use HasFactory;


    public static function get_page_image_file($files, $id) 
    {

        $image = $files[$id];

        $base_path = rtrim(config('app.url'), '/');

        $src = null;

        if(!is_null($image)) 
        {
            
            $filename = $image['file'];
            
            if(strpos($filename, 'report-templates') !== false) 
            {
                $src = $base_path . '/' . 'storage/' . $filename;
            }
            else 
            {
                $src = $base_path . $filename;
            }
        
        }


        return is_null($src) ? null : self::convert_base64($src);

    }


    public static function get_page_background_by_id($files, $index) 
    {
        
        $files = array_values($files);

        $background_index = array_search($index, array_column($files, 'page'), true);

        $base_path = rtrim(config('app.url'), '/');

        $src = null;

        if(!is_null($background_index) && $background_index !== false) 
        {
            
            $filename = $files[$background_index]['file'];
            
            if(strpos($filename, 'report-templates') !== false) 
            {
                $src = $base_path . '/' . 'storage/' . $filename;
            }
            else 
            {
                $src = $base_path . $filename;
            }
        
        }

        return is_null($src) ? null : self::convert_base64($src);
        
    }

    
    public static function get_page_background($page) 
    {
        
        $base_path = rtrim(config('app.url'), '/');
        $page = (array) $page;

        if(count((array) $page['background'])) 
        {
            $background = $page['background'];
            if($background->prefetched == true) 
            {
                $filename = $background->dataFile;
                if(strpos($filename, 'report-templates') !== false) 
                {
                    $src = $base_path . '/' . 'storage/' . $filename;
                }
                else 
                {
                    $src = $base_path . $filename;
                }
            }
            else 
            {
                $src = self::convert_resource_file('BackgroundImage#' . $background->attributes->id);
            }
            return self::convert_base64($src);
        }
    }

    public static function convert_base64($path) 
    {
        $extenstion = pathinfo($path, PATHINFO_EXTENSION);
        $data = file_get_contents($path);
        return 'data:image/' . $extenstion . ';base64,' . base64_encode($data);
    }

    public static function convert_resource_file($id) 
    {
        $src = request()->file($id)->store('pdf-images', 'public');
        $path = config('app.url') . '/' . 'storage/' . $src;
        return self::convert_base64($path);
    }

    public static function svg_base64_encode($attrs) 
    {
        $name = $attrs->name;
        $stroke = $attrs->stroke ?? 'black';
        $strokeWidth = $attrs->strokeWidth ?? 0.4;
        $width = $attrs->width ?? 100;
        $height = $attrs->height ?? 100;
        $name = self::get_svgs()[$name];
        $svg = sprintf($name, $width, $height, $stroke, $strokeWidth);
        return 'data:image/svg+xml;base64,' . base64_encode($svg);
    }

    public static function get_svgs() 
    {
        return [
            "arrow-right" => "<svg 
                viewBox='0 0 24 24' 
                width='%u'
                heigth='%u'
                stroke='%s' 
                stroke-width='%u' 
                fill='none' 
                stroke-linecap='round' 
                stroke-linejoin='round' 
                class='icon icon-tabler icon-tabler-arrow-narrow-right'
            >
                <path d='M0 0h24v24H0z' stroke='none'></path>
                <path d='M5 12h14M15 16l4-4M15 8l4 4'></path>
            </svg>",
    
            "egg" => "<svg 
                width='%u' 
                height='%u' 
                viewBox='0 0 35 25' 
                fill='none' 
                xmlns='http://www.w3.org/2000/svg'
                >
                <path 
                    d='M33 12.5C33 15.1382 31.5063 17.7154 28.7119 19.7114C25.9213 21.7046 21.9637 23 17.5 23C13.0363 23 9.07867 21.7046 6.28811 19.7114C3.49371 17.7154 2 15.1382 2 12.5C2 9.86176 3.49371 7.28463 6.28811 5.28863C9.07867 3.29538 13.0363 2 17.5 2C21.9637 2 25.9213 3.29538 28.7119 5.28863C31.5063 7.28463 33 9.86176 33 12.5Z' 
                    stroke='%s' 
                    stroke-width='%u'
                />
            </svg>",
    
            'comment-box' => "<svg 
                width='%u' 
                height='%u' 
                viewBox='0 0 36 29' fill='none' xmlns='http://www.w3.org/2000/svg'
            >
                <path 
                    d='M33 0H3C2.20435 0 1.44129 0.30125 0.87868 0.837478C0.31607 1.37371 0 2.10099 0 2.85933V25.1907C0 25.949 0.31607 26.6763 0.87868 27.2125C1.44129 27.7488 2.20435 28.05 3 28.05C3.76162 28.0459 4.49307 27.7658 5.046 27.2665L9.132 23.5952C9.24265 23.4964 9.38842 23.4413 9.54 23.4408H33C33.7957 23.4408 34.5587 23.1395 35.1213 22.6033C35.6839 22.0671 36 21.3398 36 20.5814V2.85361C35.9984 2.09626 35.6816 1.37045 35.1192 0.835455C34.5568 0.300463 33.7946 -1.5147e-06 33 0ZM33.6 20.5872C33.6 20.7388 33.5368 20.8843 33.4243 20.9915C33.3117 21.0988 33.1591 21.159 33 21.159H9.552C8.78706 21.1587 8.05088 21.4369 7.494 21.9368L3.414 25.6081C3.32876 25.6856 3.2215 25.7374 3.10557 25.7572C2.98964 25.7769 2.87018 25.7637 2.76206 25.7192C2.65395 25.6747 2.56198 25.6008 2.49759 25.5069C2.43321 25.4129 2.39927 25.3029 2.4 25.1907V2.85933C2.4 2.70766 2.46321 2.5622 2.57574 2.45496C2.68826 2.34771 2.84087 2.28746 3 2.28746H33C33.1591 2.28746 33.3117 2.34771 33.4243 2.45496C33.5368 2.5622 33.6 2.70766 33.6 2.85933V20.5872Z' 
                    stroke='%s'
                    stroke-width='%u'
                />
            </svg>"
        ];    
    }

}
