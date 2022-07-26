<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use Illuminate\Http\Request;

class UnitStatesController extends Controller
{
    
    public function index() 
    {
        return response()->json(
            Unit::all()
        );
    }

}
