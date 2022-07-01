<?php

namespace App\Http\Resources;

use App\AddressType;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Resources\Json\JsonResource;

class ActionCommercialListResource extends JsonResource
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
            'id'                 => $this->id,
            'client_name'        => $this->user->name,
            'event_name'         => $this->name,
            'event_description'  => $this->description,
            'event_type'         => optional($this->eventType)->name,
            'event_status'       => optional($this->eventStatus)->name,
            'event_status_color' => optional($this->eventStatus)->color,
            'event_origin'       => optional($this->eventOrigin)->name,
            'event_date'         => $this->datedebut,
            'event_history'      => $this->get_event_history($this),
            'order'              => !is_null($this->order) ? $this->order->load('state', 'user') : $this->order,
            'contact'            => $this->get_contact($this),
            'address'            => $this->get_address($this),
            'customer'           => $this->get_customer($this),
            'query'              => DB::getQueryLog()
        ];
    }

    private function get_customer($event) 
    {
        if(is_null($event->customer)) return (Object) [];
        return $event->customer->only([
            'raisonsociale',
            'raisonsociale2',
            'company',
            'paiement'
        ]);
    }

    private function get_event_history($event) 
    {
        if(is_null($event->eventHistory)) return [];
        return $event->eventHistory()->latest('created_at')
        ->take(3)
        ->get()
        ->load('status', 'user');
    }

    private function get_contact($event) 
    {
        if(is_null($event->contact)) return (Object) []; 
        return $event->contact
        ->only([
            'name',
            'email',
            'mobile'
        ]);
    }

    private function get_address($event) 
    {

        if(is_null($event->address)) return (Object) [];
        $address = $event->address()->where('address_type_id', 1)->first();

        if(is_null($address)) 
        {
            $address = $event->address()->where('address_type_id', 3)->first();
        }

        if(is_null($address)) 
        {
            $address = $event->address;
        }
        
        return $address->only([
            'firstname',
            'lastname',
            'address1',
            'address2',
            'postcode',
            'city',
            'address_type'
        ]);

    }

}
