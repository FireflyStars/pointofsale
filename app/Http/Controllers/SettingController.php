<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SettingController extends Controller
{
    public function getHeaderSetting(){
        $logoUrl = asset('storage/'.DB::table('settings')->where('key', 'site.logo_page')->value('value'));
        $headerColor = DB::table('settings')->where('key', 'site.color-top')->value('value');
        $faviconUrl = asset('storage/'.json_decode(DB::table('settings')->where('key', 'site.favicon')->value('value'))[0]->download_link);
        $title = DB::table('settings')->where('key', 'site.title')->value('value');
        return response()->json([
            'logoUrl'  =>  $logoUrl,
            'headerColor'  =>   $headerColor,
            'faviconUrl'   =>   $faviconUrl,
            'title'   =>   $title,
        ]);
    }
    public function getSidebarSetting(){
        $sidebarActiveColor = DB::table('settings')->where('key', 'site.color-menu-active')->value('value');
        return response()->json([
            'sidebarActiveColor'   =>   $sidebarActiveColor
        ]);
    }
    public function get404Image(){
        $imageUrl = asset('storage/'.DB::table('settings')->where('key', 'site.logo_page')->value('value'));
        $title = DB::table('settings')->where('key', 'site.title')->value('value');
        $faviconUrl = asset('storage/'.json_decode(DB::table('settings')->where('key', 'site.favicon')->value('value'))[0]->download_link);
        return response()->json([
            'imageUrl'   =>   $imageUrl,
            'faviconUrl'   =>   $faviconUrl,
            'title'   =>   $title,
        ]);
    }
    public function getLoginSetting(){
        $logoUrl = asset('storage/'.DB::table('settings')->where('key', 'site.logo_page')->value('value'));
        $loginImage = asset('storage/'.DB::table('settings')->where('key', 'site.image_login')->value('value'));
        $faviconUrl = asset('storage/'.json_decode(DB::table('settings')->where('key', 'site.favicon')->value('value'))[0]->download_link);
        $title = DB::table('settings')->where('key', 'site.title')->value('value');
        return response()->json([
            'logoUrl'   =>   $logoUrl,
            'loginImage'   =>   $loginImage,
            'faviconUrl'   =>   $faviconUrl,
            'title'   =>   $title,
        ]);
    }
}
