<?php

namespace App\Http\Controllers;

use App\Models\Pointage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PointagesController extends Controller
{
    
    public function get_data(Request $request) 
    {

        $pointages = Pointage::query();

        $pointages = $pointages->select(
            'pointage.id',
            'orders.id as commande',
            'customers.raisonsociale as client',
            'users.name as personnel',
            'numberh',
            'pointage_type_id',
            'pointage.comment',
            DB::raw('DATE_FORMAT(pointage.datepointage, "%Y-%m-%d") as datepointage')
        );

        $pointages = $pointages->leftJoin('orders', 'orders.id', '=', 'pointage.order_id')
                        ->leftJoin('order_states', 'order_states.id', '=', 'orders.order_state_id')
                        ->leftJoin('customers', 'customers.id', '=', 'orders.customer_id')
                        ->leftJoin('users', 'users.id', 'pointage.user_id');

        $data = (new TableFiltersController)->sorts($request, $pointages, 'pointage.id');
        $data = (new TableFiltersController)->filters($request, $data);

        $data = $data->take($request->take ?? 15)
        ->skip($request->skip ?? 0);

        return $data;

    }


    public function index(Request $request) 
    {

        return response()->json(
            $this->get_data($request)->get()
        );

    }

    public function pointages_mes(Request $request) 
    {

        return response()->json(
            $this->get_data($request)
            ->where('pointage.affiliate_id', $request->user()->affiliate_id)
            ->get()
        );

    }




}
