<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\TableFiltersController;
use App\Http\Resources\ContactResource;
use App\Models\ContactType;

class ContactsController extends Controller
{

    public function __construct() 
    {
        return $this->middleware(['auth']);
    }
    
    public function index(Request $request)
    {
        
        $contacts = Contact::query();
        
        $contacts->leftJoin('contact_type', 'contacts.contact_type_id', '=', 'contact_type.id')
        ->leftJoin('contact_qualite', 'contacts.contact_qualite_id', '=', 'contact_qualite.id')
        ->leftJoin('addresses', 'contacts.address_id', '=', 'addresses.id')
        ->leftJoin('customers', 'contacts.customer_id', '=', 'customers.id')
        ->leftJoin('address_type', function($join) {
            $join->on('addresses.address_type_id', '=', 'address_type.id')
            ->where('address_type.id', 1)
            ->orWhere('address_type.id', 3);
        })
        ->select(
            'contacts.id',
            'contacts.name',
            'contacts.firstname',
            'contacts.email',
            'contact_type.name as contact_type',
            'contact_type.color as contact_type_color',
            'contact_qualite.name as contact_qualite',
            'contact_qualite.color as contact_qualite_color',
            'address_type.name as address_type',
            'customers.raisonsociale as customer_name',
            'customers.id as customer_id',
        );

        $contacts = (new TableFiltersController)->sorts($request, $contacts, 'contacts.id');
        $contacts = (new TableFiltersController)->filters($request, $contacts);

        $contacts = $contacts
        ->where('customers.affiliate_id', $request->user()->affiliate_id)
        ->take($request->take ?? 15)
        ->skip($request->skip ?? 0)
        ->groupBy('contacts.id')
        ->get();



        return response()->json(
            $contacts
        );


    }

    public function get_contact_statuses() 
    {
        return response()->json(ContactType::all());
    }

    public function change_contact_status(Contact $contact, Request $request) 
    {
        $contact->contact_type_id = $request->statusId;
        $contact->deleted_at = now();
        $contact->save();
        return response()->json("Contact updated");
    }

   
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
            'orders'        => $this->get_orders($contact) 
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

    private function get_orders(Contact $contact) 
    {
        return $contact->customer->orders()
        ->latest('created_at')
        ->get()
        ->load('user', 'state');
    }

   
}
