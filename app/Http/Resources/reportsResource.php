<?php

namespace App\Http\Resources;

use App\Traits\GedFileProcessor;
use Illuminate\Support\Collection;
use Illuminate\Http\Resources\Json\JsonResource;

class ReportsResource extends JsonResource
{
    
    use GedFileProcessor;

    public function toArray($request)
    {
        return [
            'id'   => $this->id,
            'lang_id' => $this->lang_id,
            'total' => $this->total,
            'nbheure' => $this->nbheure,
            'datefinprevu' => $this->datefinprevu,
            'datecommande' => $this->datecommande,
            'affiliate' => $this->affiliate,
            'customer' => $this->get_customer_details(),
            'contact' => $this->get_contact_details(),
            'orderZones' => $this->get_order_zones($this->orderZones),
        ];
    }

    private function get_contact_details() 
    {
        $contact = optional($this->events()->latest('created_at')->first())->contact;
        return is_null($contact) ? [] : [
            'name' => $contact->name,
            'firstname' => $contact->firstname,
            'gender' => $contact->gender,
            'email' => $contact->email,
            'mobile' => $contact->mobile,
            'telephone' => $contact->telephone,
            'type' => $contact->type,
            'comment' => $contact->comment,
        ];
    }

    private function get_customer_details() 
    {
        return is_null($this->customer) ? [] : [
            'id' => $this->customer->id,
            'taxe_id' => $this->customer->taxe_id,
            'customer_group_id' => $this->customer->customer_group_id,
            'gender' => $this->customer->gender,
            'name' => $this->customer->name,
            'firstname' => $this->customer->firstname,
            'company' => $this->customer->company,
            'signupdate' => $this->customer->signupdate,
            'active' => $this->customer->active,
            'raisonsociale' => $this->customer->raisonsociale,
            'raisonsociale2' => $this->customer->raisonsociale2,
            'telephone' => $this->customer->telephone,
            'address' => $this->get_customer_address($this)
        ];
    }

    private function get_customer_address($order) 
    {
        $customer_address = $order->address; 
        if(!is_null($customer_address)) 
        {
            return $customer_address->only([
                'address1',
                'address2',
                'address3',
                'postcode',
                'city',
                'other',
                'phone',
                'phone_mobile',
                'vat_number',
                'dni',
                'active',
                'name',
                'firstname',
                'company'
            ]);
        }
    }

    private function get_order_ouvrages($zone) 
    {
        $order_categories = $this->get_order_categories($zone);
        $foramtted_ouvrages = new Collection;
        foreach($order_categories as $category) 
        {
            $ouvrage_ids = $this->get_zone_ouvrages_in_order_category($zone, $category->id);
            foreach($ouvrage_ids as $ouvrage_id) 
            {
                $foramtted_ouvrages[$category->name] = $this->get_category_ged_details($zone->gedDetails, $ouvrage_id, 'order_ouvrage_id');   
            }
        }
        return $foramtted_ouvrages;
    }

    private function get_zone_ouvrages_in_order_category($zone, $id) 
    {
        return $zone->orderOuvrage->where('order_cat_id', $id)->pluck('id')->toArray();
    }

    private function get_order_zones($orderZones) 
    {
        return $orderZones->map(function($zone) {  
            return [
                'id' => $zone->id,
                'latitude' => $zone->latitude,
                'longitude' => $zone->longitude,
                'description' => $zone->description,
                'hauteur' => $zone->hauteur,
                'name' => $zone->name,
                'moyenacces' => $zone->moyenacces,
                'gedDetails' => $this->get_formatted_ged_details($zone->gedDetails),
                'orderZoneComments' => $zone->orderZoneComments,
                'orderOuvrages' => $this->get_order_ouvrages($zone)
            ];
        });
    }

    private function get_formatted_ged_details($details) 
    {
        
        $formatted_details = new Collection;
        $categories = $this->get_ged_categories($details);
        
        foreach($categories as $category) 
        {
            $category_name = $category['name'];
            $category_id = $category['id'];
            $formatted_details[$category_name] = $this->get_category_ged_details($details, $category_id);
        }

        return $formatted_details;

    }

    private function get_category_ged_details($details, $id, $group_category = 'ged_category_id') 
    {
        return $details
        ->where($group_category, $id)
        ->map(function($detail) { 
            return [
                'id' => $detail->id,
                'ged_id' => $detail->ged_id,
                'order_zone_id' => $detail->order_zone_id,
                'order_ouvrage_id' => $detail->order_ouvrage_id,
                'user_id' => $detail->user_id,
                'description' => $detail->description,
                'file' => $detail->file,
                'human_readable_filename' => $detail->human_readable_filename,
                'storage_path' => $detail->storage_path,
                'type' => $detail->type,
                'longitude' => $detail->longitude,
                'latitude' => $detail->latitude,
                'timeline' => $detail->timeline,
                'urls'   => $this->getFileUrls($detail),
            ];
        });
    }

    private function get_order_categories($zone) 
    {
        $ouvrage_categories = new Collection;
        foreach($zone->orderOuvrage as $ouvrage) 
        {
            $ouvrage_categories []= $ouvrage->orderCategory;
        }
        return $ouvrage_categories;
    }

    private function get_ged_categories($ged_details) 
    {
        return $ged_details
        ->map(function($item) { return $item->gedCategory; })
        ->filter(function($item) { return !is_null($item); })
        ->map(function($item) { 
            return [
                'id' => $item->id,
                'name' => $item->name
            ];
        });
    }

}
