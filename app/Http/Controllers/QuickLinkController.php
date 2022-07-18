<?php

namespace App\Http\Controllers;

use App\Models\QuickLink;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QuickLinkController extends Controller
{
    //

    public function addLink(Request $request){
        $name=$request->post('name');
        $route=$request->post('route');
        $background_color=$request->post('background_color');
        $font_color=$request->post('font_color');
        $icon_name=$request->post('icon_name');
        $type=$request->post('type');
        $page_route=$request->post('page_route');

        $ql=new QuickLink();
        $ql->page_route=$page_route;
        $ql->name=$name;
        $ql->route=$route;
        $ql->icon_name=$icon_name;
        $ql->external=$type;
        $ql->background_color=$background_color;
        $ql->font_color=$font_color;
        $ql->save();
        $ql->fresh();
        return response()->json(array('link'=>$ql));
      
    }

    public function getLinks(Request $request){
        $user=Auth::user();
        $roles=$user->getRoles();
        $is_admin=false;
        foreach($roles as $role){
            if($role->name=='admin')
            $is_admin=true;
        }
        return response()->json(array('links'=>QuickLink::all(),'is_admin'=>$is_admin));
    }

    public function removeLink(Request $request){
        $quicklink_id=$request->post('quicklink_id');
        $ql=QuickLink::find($quicklink_id);
        $ql->delete();
    }
}
