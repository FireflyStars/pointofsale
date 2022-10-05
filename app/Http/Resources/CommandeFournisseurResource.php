<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CommandeFournisseurResource extends JsonResource
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
            'id' => $this->id,
            'supplier' => $this->supplier->load('type'),
            'user' => $this->user,
            'state' => $this->state,
            'total' => $this->total,
            'tax' => $this->tax,
            'reference' => $this->reference,
            'dateinvoice' => $this->dateinvoice,
            'created_at' => $this->created_at
        ];
    }
}
