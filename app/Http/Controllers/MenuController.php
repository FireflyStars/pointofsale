<?php

namespace App\Http\Controllers;

use App\Models\menu_front;

class MenuController extends Controller
{
    
    public function index() 
    {

        return response()->json(
            menu_front::with('children')
            ->where('parent_id', 0)
            ->orderBy('order', 'ASC')
            ->get()
        );    

    }

}
