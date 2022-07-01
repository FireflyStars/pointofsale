<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Event;
use App\Models\EventStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\TableFiltersController;
use App\Http\Resources\ActionCommercialListResource;

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

    public function change_event_status(Event $event, Request $request) 
    {

        $event->event_status_id = $request->statusId;

        if($request->annuler == false) 
        {
            $event->deleted_at = now();
        }

        $event->save();

        $event->eventHistory()->create([
            'event_statut_id' => 17,
            'comment'         => "Effacer event",
            'user_id'         => $request->user()->id,
        ]);

        return response()->json("Event status changed");

    }

    public function get_event_history(Event $event, Request $request) 
    {
        return response()->json(
            $event->eventHistory()
            ->latest('created_at')
            ->when($request->has('limit') && $request->limit > 0, function($query) {
                $query->limit('3');
            })
            ->get()
            ->load('user', 'status')
        );
    }

    public function get_details($id) 
    {
        DB::enableQueryLog();
        return response()->json(
            new ActionCommercialListResource(
                Event::find($id)
            )
        );

    }

    public function get_event_users(Event $event) 
    {
        return response()->json(
            User::where('affiliate_id', $event->affiliate_id)->get()
        );
    }

    public function get_event_statuses() 
    {
        return response()->json(
            EventStatus::all()
        );   
    }

    public function change_event_user(Event $event, Request $request) 
    {
        $event->user_id = $request->userId;
        $event->save();
        return response()->json($event->user);
    }

    public function change_event_date(Request $request, Event $event) 
    {

        $datedebut = Carbon::parse($event->datedebut)->format('Y-m-d');
        $datefin = Carbon::parse($event->datefin)->format('Y-m-d');
        
        $event->update([
            'datedebut' => $request->datedebut . ' ' . $request->datedebutTime . ':00',
            'datefin'   => $datefin . ' ' . $request->datefinTime . ':00'
        ]);

        $status = EventStatus::where('name', 'Replanification')->first();

        $newDateDebut = Carbon::parse($event->datedebut)->format('Y-m-d');

        $event->eventHistory()->create([
            'event_statut_id' => $status->id ?? 17,
            'comment'         => "$datedebut (Changed date: $newDateDebut, start hours: $request->datedebutTime)",
            'user_id'         => $request->user()->id,
        ]);

        return response()->json("Event dates updated");

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
