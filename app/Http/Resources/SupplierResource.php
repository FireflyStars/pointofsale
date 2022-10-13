<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SupplierResource extends JsonResource
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
            'id'          => $this->id,
            'actif'       => $this->actif,
            'contact'     => $this->get_formatted_contact($this),
            'created_at'  => $this->created_at,
            'type'        => $this->type,
            'status'      => $this->status,
            'orders'      => $this->orders,   
        ];
    }

    private function get_formatted_contact($supplier) 
    {
        return $supplier->contactname . '<br>' . $supplier->phone;
    }

}
