<?php

namespace App\Http\Resources;

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
            'origine'       => $this->origine,
            'litige'        => $this->litige,
            'comment'       => $this->comment,
            'contacts'      => $this->contacts,
            'addresses'     => $this->addresses,
            'status'        => $this->status,
            'orders'        => $this->orders,
        ];
    }
}
