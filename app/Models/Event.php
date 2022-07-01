<?php

namespace App\Models;

use Exception;
use App\Traits\LcdtLog;
use App\Models\EventStatus;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;


use function PHPUnit\Framework\throwException;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Event extends Model
{
    use HasFactory;
    use LcdtLog;
    use SoftDeletes;

    protected $guarded = ['id'];


    public function customer(){
        return $this->belongsTo(Customer::class);
    }
    public function user(){
        return $this->belongsTo(User::class);
    }
    public function address(){
        return $this->belongsTo(Address::class);
    }
    public function status(){
        return $this->belongsTo(Status::class);
    }

    public function order(){
        return $this->belongsTo(Order::class);
    }

    public function save(array $options = []){
        
        $user=Auth::user();
        if($user)
        $this->affiliate_id=$user->affiliate->id;

        return  parent::save($options);
      }

     public function updateStatus($status_id,$user_id=null){
         $eventHistory= new EventHistory();
         if($user_id==null){
            $user=Auth::user();
         }else{
             $user=User::find($user_id);
         }
         if($user==null)
         throw new Exception('Cannot update status without user authentication.');
         $eventHistory->user_id=$user->id;
         $eventHistory->event_id=$this->id;
         $eventHistory->event_statut_id=$status_id;
         $this->event_status_id=$status_id;
         $this->save();
         $eventHistory->save();
         $this->l('EVENT STATUS UPDATE','status_id',$user->id);
     } 

     public function eventType(){
         return $this->belongsTo(EventType::class);
     }

     public function eventOrigin(){
         return $this->belongsTo(EventOrigin::class);
    }

    
    public function eventComments(){
        return $this->hasMany(EventComment::class);
   }

   public function contact(){
       return $this->belongsTo(Contact::class);
   }

   public function eventStatus() 
   {
        return $this->belongsTo(EventStatus::class);
   }

   public function eventHistory() 
   {
        return $this->hasMany(EventHistory::class);
   }

}
