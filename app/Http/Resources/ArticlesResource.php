<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ArticlesResource extends JsonResource
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
            'name'               => $this->name,
            'description'        => $this->description,
            'type'               => $this->type,    
            'price'              => number_format($this->wholesale_price, 2),
            'wholesale_price'    => $this->get_wholesale_price($this, $request),
            'unit'               => $this->unit,
            'reference'          => $this->reference,
            'supplier_reference' => $this->supplier_reference,
        ];
    }


    public function get_wholesale_price($product, $request) 
    {
        if($product->productAffiliate()->where('affilie_id', $request->user()->affiliate_id)->count()) 
        {
            $product = $product->productAffiliate()
                ->where('affilie_id', $request->user()->affiliate_id)
                ->latest('created_at')
                ->first();
        } 
        return number_format($product->wholesale_price, 2);
    }

}
