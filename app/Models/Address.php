<?php

namespace App\Models;

use App\AddressType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Address extends Model
{
    use HasFactory;

    
    public function getformattedAddressWithName()
    {
        return $this->gender.' '.$this->firstname.' '.$this->lastname.'<br/>'.$this->getformattedAddress();
    }

    public function getformattedAddress()
    {
        return $this->address1.(trim($this->address2)!=''?'<br/>'.$this->address2:'').(trim($this->address3)!=''?'<br/>'.$this->address3:'').'<br/>'.$this->postcode.' '.$this->city;
    }

    public function address_type() 
    {
        return $this->belongsTo(AddressType::class, 'address_type_id');
    }

}
