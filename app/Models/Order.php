<?php

namespace App\Models;

use App\Traits\Tools;
use App\Models\Report;
use App\Models\Contact;

use App\Traits\LcdtLog;
use App\Models\Customer;
use App\Models\Affiliate;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory;
    use Tools;
    use LcdtLog;


    public function events(){
        return $this->hasMany(Event::class);
    }

    public function customer(){
        return $this->belongsTo(Customer::class);
    }

    public function orderZones(){
        return $this->hasMany(OrderZone::class);
    }

    public function orderOuvrages(){
        return $this->hasMany(OrderOuvrage::class);
    }

    public function state(){
        return $this->belongsTo(OrderState::class);
    }

    public function geds(){
        return $this->hasMany(Ged::class);
    }

    public function contact() 
    {
        return $this->belongsTo(Contact::class, 'responsable_id');
    }

    public function report() 
    {
        return $this->hasOne(Report::class);
    }

    public function affiliate() 
    {
        return $this->belongsTo(Affiliate::class);
    }

    public function updateState($order_state_id, $user_id=null){
        if($user_id==null)
            $user_id=Auth::user()->id;
        $user=User::find($user_id);
        if($user->affiliate->id!=$this->affiliate_id)
        throw new Exception('Order does not affiliated to user.');

        $orderState=OrderState::find($order_state_id);
        if($orderState==null)
        throw new Exception('Invalid order state.');

        $previous_order_state_id=$this->order_state_id;
        if($this->order_state_id!=$order_state_id){
            $this->order_state_id=$order_state_id;
            if($this->order_state_i)
            $orderHistory=new OrderHistory();
            $orderHistory->order_state_id=$order_state_id;
            $orderHistory->user_id=$user_id;
            $orderHistory->order_id=$this->id;
            $this->save();
            $orderHistory->save();
            $this->l('ORDER STATE UPDATED','Order #'.$this->id.': status changed '.($previous_order_state_id==null?'':'from '.$previous_order_state_id).' to '.$order_state_id,$user_id);
        }
    }

    public function generateReference(){
        $this->reference= strtoupper($this->passwdGen(10,'NO_NUMERIC'));
    }

    public function address(){
        return $this->belongsTo(Address::class);
    }
}
