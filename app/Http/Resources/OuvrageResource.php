<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OuvrageResource extends JsonResource
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
            'name' => $this->name,
            'reference' => $this->codelcdt,
            'description' => $this->textchargeaffaire,
            'textcustomer' => $this->getOuvrageTextcustomer($this),
            'ouvrage_affiliate' => $this->OuvrageAffiliate,
            'prestation' => $this->prestation,
            'metier' => $this->metier,
            'toit' => $this->toit,
            'unit' => $this->unit,
            'tasks' => $this->getTasks($this)
        ];
    }

    public function getOuvrageTextcustomer($ouvrage) 
    {
        $ouvrageTextcustomer = optional($ouvrage->OuvrageAffiliate)->textcustomer;
        return is_null($ouvrageTextcustomer) ? $ouvrage->textcustomer : $ouvrageTextcustomer;
    }

    public function getTasks($ouvrage) 
    {
        return $ouvrage->tasks
            ->load(
                'OuvrageAffiliate', 
                'unit', 
                'details', 
                'details.OuvrageAffiliate'
            );
    }

}
