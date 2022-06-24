<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\TableFiltersController;

class ActionCommercialListController extends Controller
{
    public function index(Request $request) 
    {

        $data = $this->get_data($request);

        $data = $data->where('events.affiliate_id', $request->user()->affiliate_id)
        ->take($request->take ?? 15)
        ->skip($request->skip ?? 0)
        ->get();            

        return response()->json($data);  

    }


    public function list_user(Request $request) 
    {

        $data = $this->get_data($request);

        $data = $data->where('events.user_id', $request->user()->id)
        ->take($request->take ?? 15)
        ->skip($request->skip ?? 0)
        ->get();            

        return response()->json($data); 

    }


    public function statuses_formatted() 
    {
        return response()->json(
            EventStatus::select('id', 'name as value')->get()
        );
    }

    public function statuses() 
    {
        return response()->json(
            EventStatus::all()
        );
    }


    private function get_data(Request $request) 
    {

        $data = Event::query()
        ->leftJoin('contacts', 'contacts.id', '=', 'events.contact_id')
        ->leftJoin('addresses', 'addresses.id', '=', 'events.address_id')
        ->leftJoin('users', 'users.id', '=', 'events.user_id')
        ->leftJoin('event_types', 'event_types.id', '=', 'events.event_type_id')
        ->leftJoin('event_statuses', 'event_statuses.id', '=', 'events.event_status_id')
        ->leftJoin('event_origins', 'event_origins.id', '=', 'events.event_origin_id')
        ->select(
            'events.id',
            'users.name as client_name',
            'events.name as action',
            'event_types.name as event_type',
            'event_statuses.id as event_status_id',
            'event_origins.name as origin',
            DB::raw('DATE_FORMAT(events.datedebut, "%Y-%m-%d") as action_date'),
            DB::raw(
                'TRIM(
                    CONCAT(
                        contacts.name,
                        "<br>",
                        contacts.email,
                        "<br>",
                        contacts.mobile
                    )
                ) 
            as contact'), 
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
            DB::raw('DATE_FORMAT(events.created_at, "%Y-%m-%d") as created_at'),
        );

        $data = (new TableFiltersController)->sorts($request, $data, 'events.id');
        $data = (new TableFiltersController)->filters($request, $data);


        return $data;

    }

}
