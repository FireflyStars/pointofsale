<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PaiementResource extends JsonResource
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
            'invoice_id'    => $this->invoice_id,
            'percentage'    => $this->pourcentage,
            'montant'       => $this->montantpaiement,
            'datepaiement'  => $this->datepaiement,
            'reference'     => $this->reference,
            'type'          => $this->type,
            'state'         => $this->state,
            'customer'      => $this->get_customer($this),
            'user'          => $this->user,
            'history'       => $this->get_history($this),
        ];
    }


    public function get_history($paiement) 
    {
        return $paiement->history()
        ->latest('created_at')
        ->take(3)
        ->get()
        ->load('state', 'user');
    }

    public function get_customer($paiement) 
    {
        return $paiement->customer()
        ->select('id', 'name')
        ->first();
    }

}
