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

    public static function getFacturationAddress($customer_id){


//         addresses_type=1 or
//  if there arent type1 >> type=3
// if  there arent type1  and 3>> the adresse
// if there arent address >> ‘Pas d adresse cliente’

$address=Address::where('customer_id','=',$customer_id)->where('address_type_id','=',1)->whereNull('deleted_at')->first();
if($address==null)
$address=Address::where('customer_id','=',$customer_id)->where('address_type_id','=',3)->whereNull('deleted_at')->first();
if($address==null)
$address=Address::where('customer_id','=',$customer_id)->whereNull('deleted_at')->first();

return $address;
    }

}
