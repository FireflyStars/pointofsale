<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ContactResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id'              => $this->id,
            'firstname'       => $this->firstname,
            'name'            => $this->name,
            'contact_qualite' => $this->contact_qualite,
            'contact_type'    => $this->contact_type,
            'customer'        => $this->get_customer($this),
            'address'         => $this->get_address($this), 
            'event_history'   => $this->get_event_history($this),
            'orders'          => $this->get_orders($this)    
        ];
    }

    private function get_orders($contact) 
    {
        return $contact->customer->orders()
        ->take(3)
        ->latest('created_at')
        ->get()
        ->load('user', 'state');
    }
    
    private function get_customer($contact) 
    {
        return optional($contact->customer)->only([
            'raisonsociale',
            'raisonsociale2',
            'company',
            'paiement'
        ]);
    }

    public function get_address($contact) 
    {   
        
        if(is_null($contact->address)) return (Object) [];

        $address = $contact->address()->where('address_type_id', 1)->first();

        if(is_null($address)) 
        {
            $address = $contact->address()->where('address_type_id', 3)->first();
        }

        if(is_null($address)) 
        {
            $address = $contact->address;
        }

        return $address
        ->only([
            'firstname',
            'lastname',
            'address1',
            'address2',
            'postcode',
            'city',
            'address_type',
        ]);


    }

    private function get_event_history($contact) 
    {
        if(is_null($contact->eventHistory)) return [];
        return $contact->eventHistory()->latest('created_at')
        ->take(3)
        ->get()
        ->load('status', 'user');
    }


}
