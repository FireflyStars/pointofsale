<?php

namespace App\Http\Controllers;

use App\Http\Resources\EntiteResource;
use App\Models\Order;
use App\Models\Address;
use App\Models\Contact;
use App\Models\Customer;
use App\Models\CustomerStatut;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EntiteController extends Controller
{

    public function index(Request $request) 
    {

        $contacts = Contact::query()
                    ->select(
                        'customer_id',
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
                        as contact')
                    )->groupBy('customer_id');
        
                    
        $addresses = Address::query()
                    ->select(
                        'customer_id',
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
                    )
                    ->groupBy('customer_id');


        $orders = Order::query()
                 ->select(
                    'customer_id',
                    DB::raw('orders.total as montant'),
                    DB::raw('COUNT(orders.id) as total_orders'),
                 )
                 ->groupBy('customer_id');

        
        $status = CustomerStatut::query()
                ->select(
                    'id',
                    'name',
                    'color'
                )
                ->groupBy('id');

        $events = Event::query()
                  ->select(
                    'customer_id',
                    'name',
                    DB::raw('DATE_FORMAT(datedebut, "%Y-%m-%d") as datedebut'),
                  )
                  ->groupBy('customer_id');        
                    

        $customers = Customer::query()
                    ->select(
                        'customers.id', 
                        'raisonsociale',
                        'customers.origine',
                        'customers.litige',
                        'customers.comment',
                        'contacts.contact as contact',
                        'addresses.address as address',
                        'status.name as statut_name',
                        'status.color as statut_color',
                        DB::raw('IFNULL(orders.montant, 0) as montant'),
                        DB::raw('IFNULL(orders.total_orders, 0) as total_orders'),
                        DB::raw('CONCAT(events.datedebut, "<br>", events.name) as action_co'),
                        DB::raw('DATE_FORMAT(customers.created_at, "%Y-%m-%d") as created_at')
                    )
                    ->leftJoinSub($contacts, 'contacts', function($join) {
                        $join->on('contacts.customer_id', '=', 'customers.id');
                    })
                    ->leftJoinSub($addresses, 'addresses', function($join) {
                        $join->on('addresses.customer_id', '=', 'customers.id');
                    })
                    ->leftJoinSub($orders, 'orders', function($join) {
                        $join->on('orders.customer_id', '=', 'customers.id');
                    })
                    ->leftJoinSub($status, 'status', function($join) {
                        $join->on('status.id', '=', 'customers.customer_statut_id');
                    })
                    ->leftJoinSub($events, 'events', function($join) {
                        $join->on('events.customer_id', '=', 'customers.id')
                        ->where('events.datedebut', '>', now()->format('Y-m-d H:i:s'));
                    });

        
        $data = (new TableFiltersController)->sorts($request, $customers, 'customers.id');
        $data = (new TableFiltersController)->filters($request, $data);

        $data = $data->where('customers.affiliate_id', $request->user()->affiliate_id)
        ->take($request->take ?? 15)
        ->skip($request->skip ?? 0)
        ->groupBy('customers.id')
        ->get();            


        return response()->json($data);            


    }

    public function get_details(Customer $customer) 
    {
        return response()->json(
            new EntiteResource($customer)
        );
    }


    public function change_entite_actif(Customer $customer) 
    {

        $customer->update([
            'active'             => 0,
            'customer_statut_id' => 6,
            'deleted_at'         => now()
        ]);

        return response()->json("Customer updated");

    }


    public function change_entite_litige(Customer $customer, Request $request) 
    {
        $customer->update([
            'litige' => 1,
            'comment' => $request->comment
        ]);

        return response()->json("Customer updated");

    }

    public function get_entite_results(Customer $customer, Request $request) 
    {

        $results = [];

        $types = [
            'event_history' => $this->get_event_history($customer), 
            'orders'        => $this->get_orders($customer), 
            'event_invoices'=> $this->get_invoices($customer)
        ];

        $results = $types[$request->type];

        return response()->json($results);

    }

    private function get_orders($customer) 
    {
        return $customer->orders()
        ->latest('created_at')
        ->get()
        ->load('user', 'state');
    }


    private function get_event_history($customer) 
    {
        return $customer->eventsHistory()
            ->latest('created_at')
            ->get()
            ->load('user', 'status', 'event');
    }

    private function get_invoices($customer) 
    {
        return $customer->invoices()
            ->latest('created_at')
            ->get()
            ->load('state');
    }


}
