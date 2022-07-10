<?php

namespace App\Http\Resources;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Resources\Json\JsonResource;

class EntiteResource extends JsonResource
{
  
    public function toArray($request)
    {
        return [
            'id'              => $this->id,
            'raisonsociale'   => $this->raisonsociale,
            'raisonsociale2'  => $this->raisonsociale2,
            'company'         => $this->company,
            'origin'          => $this->customerOrigin,
            'paiement'        => $this->paiement,
            'litige'          => $this->litige,
            'active'          => $this->active,
            'comment'         => $this->comment,
            'created_at'      => $this->created_at,
            'contact'         => $this->get_contacts($this),
            'address'         => $this->get_addresses($this),
            'status'          => $this->status,
            'orders'          => $this->get_orders($this),
            'event_history'   => $this->get_event_history($this),
            'event_invoices'  => $this->get_invoices($this),
        ];
    }

    private function get_contacts($customer) 
    {
        return $customer->contacts()
        ->select('firstname', 'email', 'mobile')
        ->take(1)
        ->latest('created_at')
        ->first();
    }

    private function get_addresses($customer) 
    {

        if(is_null($customer->addresses) && !count($customer->addresses)) return (Object) [];

        $address = $customer->addresses()->where('address_type_id', 1)
        ->take(1)
        ->latest('created_at')
        ->first();

        if(is_null($address)) 
        {
            $address = $customer->addresses()->where('address_type_id', 3)
            ->take(1)
            ->latest('created_at')
            ->first();
        }

        if(is_null($address)) 
        {
            $address = $customer->addresses()
            ->take(1)
            ->latest('created_at')
            ->first();
        }

        return $address->only([
            'firstname',
            'lastname',
            'address1',
            'address2',
            'postcode',
            'city',
            'address_type',
        ]);

    }

    private function get_orders($customer) 
    {
        return $customer->orders()
        ->take(3)
        ->latest('created_at')
        ->get()
        ->load('user', 'state');
    }


    private function get_event_history($customer) 
    {
        return $customer->eventsHistory()
            ->take(3)
            ->latest('created_at')
            ->get()
            ->load('user', 'status', 'event');
    }

    private function get_invoices($customer) 
    {
        return $customer->invoices()
            ->take(3)
            ->latest('created_at')
            ->get()
            ->load('state');
    }

}
