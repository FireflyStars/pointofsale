<?php

namespace App\Http\Controllers;

use App\Models\Intervention;
use App\Models\InterventionStatus;
use App\Models\InterventionType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InterventionsController extends Controller
{
    
    public function get_data(Request $request) 
    {

        $interventions = Intervention::query();

        $interventions = $interventions->select(
            'interventions.id',
            'interventions.name as client_name',
            'orders.id as commande',
            'users.name as affecte_a',
            'intervention_types.name as intervention_type',
            'intervention_statut_id',
            DB::raw(
                'TRIM(
                    CONCAT(
                        addresses.firstname, " ", addresses.lastname,
                        addresses.address1,
                        "<br>",
                        IFNULL(addresses.address2, ""),
                        IF(addresses.address2, "<br>", ""),
                        addresses.postcode,
                        "<br>", 
                        addresses.city
                    )
                ) 
            as address'),
            DB::raw('DATE_FORMAT(interventions.datedebut, "%Y-%m-%d") as dateaction'),
        );

        $interventions = $interventions->leftJoin('orders', 'orders.id', '=', 'interventions.order_id')
                        ->leftJoin('users', 'users.id', '=', 'interventions.user_id')
                        ->leftJoin('intervention_types', 'intervention_types.id', '=', 'interventions.intervention_type_id')
                        ->leftJoin('addresses', 'addresses.id', '=', 'orders.address_id');

        $data = (new TableFiltersController)->sorts($request, $interventions, 'interventions.id');
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

    public function interventions_mes(Request $request) 
    {

        return response()->json(
            $this->get_data($request)
            ->where('interventions.affiliate_id', $request->user()->affiliate_id)
            ->get()
        );

    }

    public function get_intervention_status_formatted() 
    {
        return response()->json(
            InterventionStatus::select('id', 'name as value')
                        ->get()
        );    
    }

    public function get_intervention_status() 
    {
        return response()->json(
            InterventionStatus::all()
        );    
    }

    public function get_intervention_types() 
    {
        return response()->json(
            InterventionType::select('id', 'name as value')
                        ->get()       
        );
    }


}
