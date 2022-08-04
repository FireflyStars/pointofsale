<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function index(){
        return response()->json(
            DB::table('homenews')->where('affiliat_id', auth()->user()->affiliate_id)->value('code')
        );
    }
}