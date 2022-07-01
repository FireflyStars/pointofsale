<?php

namespace App\Http\Resources;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Resources\Json\JsonResource;

class EntiteResource extends JsonResource
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
            'id'            => $this->id,
            'raisonsociale' => $this->raisonsociale,
            'raisonsociale2'=> $this->raisonsociale2,
            'company'       => $this->company,
            'origine'       => $this->origine,
            'litige'        => $this->litige,
            'active'        => $this->active,
            'comment'       => $this->comment,
            'contacts'      => $this->contacts,
            'addresses'     => $this->addresses,
            'status'        => $this->status,
            'orders'        => $this->orders()->take(3)->get(),
            'event_history' => $this->get_event_history($this),
            'event_invoices' => $this->invoices,
            'query'         => DB::getQueryLog()
        ];
    }


    private function get_event_history($customer) 
    {
        return $customer->eventsHistory()
            ->take(3)
            ->get()
            ->load('user', 'status');
    }

}
