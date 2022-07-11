<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\TableFiltersController;
use App\Http\Resources\ContactResource;

class ContactsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        
        $contacts = Contact::query();
        
        $contacts->leftJoin('contact_type', 'contacts.contact_type_id', '=', 'contact_type.id')
        ->leftJoin('contact_qualite', 'contacts.contact_qualite_id', '=', 'contact_qualite.id')
        ->leftJoin('addresses', 'contacts.address_id', '=', 'addresses.id')
        ->leftJoin('address_type', function($join) {
            $join->on('addresses.address_type_id', '=', 'address_type.id')
            ->where('address_type.id', 1)
            ->orWhere('address_type.id', 3);
        })
        ->select(
            'contacts.id',
            'contacts.name',
            'contacts.firstname',
            'contact_type.name as contact_type',
            'contact_type.color as contact_type_color',
            'contact_qualite.name as contact_qualite',
            'contact_qualite.color as contact_qualite_color',
            'address_type.name as address_type',
            DB::raw('DATE_FORMAT(contacts.created_at, "%Y-%m-%d") as created_at')
        );

        $contacts = (new TableFiltersController)->sorts($request, $contacts, 'contacts.id');
        $contacts = (new TableFiltersController)->filters($request, $contacts);

        $contacts = $contacts
        ->take($request->take ?? 15)
        ->skip($request->skip ?? 0)
        ->groupBy('contacts.id')
        ->get();



        return response()->json(
            $contacts
        );


    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Contact $contact)
    {
        return response()->json(
            new ContactResource($contact)
        );
    }

    public function contact_results(Contact $contact, Request $request) 
    {
        
        $results = [];

        $types = [
            'event_history' => $this->get_event_history($contact), 
        ];

        $results = $types[$request->type];

        return response()->json($results);

    }

    private function get_event_history(Contact $contact) 
    {
        return $contact->eventHistory()
            ->latest('created_at')
            ->get()
            ->load('user', 'status');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
