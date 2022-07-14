<?php

namespace App\Models;

use App\Models\Event;
use App\Models\Order;
use App\Models\Address;
use App\Models\Customer;
use App\Models\ContactType;
use App\Models\EventHistory;
use App\Models\ContactQualite;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contact extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];

    public function contact_qualite() 
    {
        return $this->belongsTo(ContactQualite::class);
    }

    public function contact_type() 
    {
        return $this->belongsTo(ContactType::class);
    }

    public function address() 
    {
        return $this->belongsTo(Address::class);
    }

    public function customer() 
    {
        return $this->belongsTo(Customer::class);
    }

    public function events() 
    {
        return $this->hasMany(Event::class);
    }

    public function eventHistory() 
    {
        return $this->hasManyThrough(EventHistory::class, Event::class);
    }

}
