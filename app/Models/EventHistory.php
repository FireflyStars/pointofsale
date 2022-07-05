<?php

namespace App\Models;

use App\Models\User;
use App\Models\Event;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EventHistory extends Model
{
    use HasFactory;

    protected $table="event_history";
    protected $guarded = ['id'];

    public function status() 
    {
        return $this->belongsTo(EventStatus::class, 'event_statut_id');
    }

    public function event() 
    {
        return $this->belongsTo(Event::class);
    }

    public function user() 
    {
        return $this->belongsTo(User::class);
    }

    
}
