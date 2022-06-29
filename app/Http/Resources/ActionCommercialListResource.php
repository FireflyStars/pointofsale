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
            'event_type'         => $this->eventType->name,
            'event_status'       => $this->eventStatus->name,
            'event_status_color' => $this->eventStatus->color,
            'event_origin'       => $this->eventOrigin->name,
            'event_date'         => $this->datedebut,
            'event_history'      => $this->get_event_history($this),
            'order'              => !is_null($this->order) ? $this->order->load('state', 'user') : $this->order,
            'contact'            => $this->get_contact($this),
            'address'            => $this->get_address($this),
            'paiement'           => $this->customer->paiement, 
            'address_type'       => $this->get_address_type($this),
            'query'              => DB::getQueryLog()   
        ];
    }

    private function get_event_history($event) 
    {
        return $event->eventHistory()->latest('created_at')
        ->take(5)
        ->get()
        ->load('status', 'user');
    }

    private function get_address_type($event) 
    {
        $type = optional($event->address)->address_type;
        return is_null($type) ? AddressType::find(3) : $type;
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
        return $event->address
        ->only([
            'firstname',
            'lastname',
            'address1',
            'address2',
            'postcode',
            'city'
        ]);
    }

}
