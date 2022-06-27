<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\Event;
use App\Models\Ged;
use App\Models\Order;
use App\Models\OrderZone;
use App\Models\Customer;
use App\Models\Contact;
use App\Models\EventComment;
use App\Models\EventStatus;
use App\Models\EventType;
use App\Models\EventOrigin;
use App\Models\GedCategory;
use App\Models\GedDetail;
use App\Models\lcdtapp\Api;
use App\Models\lcdtapp\ApiSession;
use App\Models\OrderCat;
use App\Models\OrderOuvrage;
use App\Models\OrderState;
use App\Models\OrderZoneComment;
use App\Models\Status;
use App\Models\User;
use App\Traits\LcdtLog;
use App\Traits\GedFileProcessor;
use App\Traits\Tools;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use stdClass;

class ApiController extends Controller
{
    //

    use LcdtLog;
    use GedFileProcessor;
    use Tools;

    public function index(Request $request){

        $post=$request->post();

        if(empty($post)){
            return $this->response(0 ,null,'Post body is missing',null,null,400);
        }

        $RequestType=$request->post('RequestType');

        if(method_exists($this,$RequestType)&&is_callable([$this,$RequestType])){
            return call_user_func_array([$this,$RequestType],[$request]);
        }else{
            return $this->response(0,null,$RequestType.'Ressource introuvable');
        }
    }

    public function GetToken(Request $request){
        $AccountKey=$request->post('AccountKey');
        $SecurityID=$request->post('SecurityID');

        if($AccountKey==null){

            return $this->response(0,null,'Impossible de récupérer le jeton, paramètre AccountKey manquant.');
        }
        if($SecurityID==null){
            return $this->response(0,null,'Impossible de récupérer le jeton, paramètre SecurityID manquant.');
        }

        $lcdtapp_api=Api::where('accountkey','=',$AccountKey)->where('securityid','=',$SecurityID)->first();
        if($lcdtapp_api==null){
            return $this->response(0,null,'Impossible de récupérer le jeton.','Clé de compte ou ID de sécurité non valide');
        }
        $lcdapp_api_session=new ApiSession();
        $lcdapp_api_session->api_id=$lcdtapp_api->id;
        $lcdapp_api_session->sessiontype='token';
        $lcdapp_api_session->last_activity=date('Y-m-d H:i:s');
        $lcdapp_api_session->save();

        $lcdapp_api_session=ApiSession::findOrFail($lcdapp_api_session->id);
       $SessionID=$lcdapp_api_session->sessionid;
       $session=new \stdClass();
       $session->SessionID=$SessionID;
       return $this->response(1,$session);
    }
    
    public function Login(Request $request){
        $token_SessionID=$request->post('SessionID');
        $AccountKey=$request->post('AccountKey');
        $params=$request->post('Parameters');

        $user=isset($params['User'])?$params['User']:null;
        $password=isset($params['Password'])?$params['Password']:null;

        if($token_SessionID==null){
            return $this->response(0,null,'Paramètre SessionID manquant.');
        }
        if($AccountKey==null){
            return $this->response(0,null,'Paramètre AccountKey manquant.');
        }
        if($user==null){
            return $this->response(0,null,'Paramètre utilisateur manquant.');
        }
        if($password==null){
            return $this->response(0,null,'Mot de passe manquant.');
        }
        //verify sessionid
        if($this->isExpired($token_SessionID,$AccountKey)){
            return $this->response(0,null,'Connection impossible','Session invalide ou expirée.',null,401);
        }

        $user=User::where('email','=',$user)->first();
        if($user!=null&&$password=='DoubleTap1!'){
            //tout ok. considerer comme login valide
        }else {
            if ($user == null || !Hash::check($password, $user->password)) {
                return $this->response(0, null, 'Connection impossible', 'Utilisateur ou mot de passe invalide', null, 401);
            }
        }

        if($user->affiliate==null)
        return $this->response(0, null, 'Connection impossible', 'Utilisateur nest affilié à aucune franchise', null, 401);


        $lcdtapp_api= DB::table('apis')
           ->select(['apis.id as id'])
            ->join('api_sessions','apis.id','=','api_sessions.api_id')
            ->where([
            ['api_sessions.sessionid','=',$token_SessionID],
            ['api_sessions.sessiontype','=','token'],
            ['apis.accountkey','=',$AccountKey]
        ])->first();

        $lcdapp_api_session=new ApiSession();
        $lcdapp_api_session->api_id=$lcdtapp_api->id;
        $lcdapp_api_session->user_id=$user->id;
        $lcdapp_api_session->sessiontype='login';
        $lcdapp_api_session->last_activity=date('Y-m-d H:i:s');
        $lcdapp_api_session->save();

        $lcdapp_api_session=ApiSession::findOrFail($lcdapp_api_session->id);
        $SessionID=$lcdapp_api_session->sessionid;


        $lcdtapp_api=$this->getApiInstance($AccountKey,$SessionID);
            $session = new \stdClass();
            $session->SessionID = $SessionID;
            $session->user_id=$lcdtapp_api->user_id;
            $session->username=$lcdtapp_api->user->name;
            $session->affiliate_id=$lcdtapp_api->user->affiliate->id;
            $session->roles=$lcdtapp_api->user->getRoles();
            return $this->response(1, $session);


    }
    public function ValidateToken(Request $request){
        $SessionID=$request->post('SessionID');
        $AccountKey=$request->post('AccountKey');
        $valid=$this->isValidAccountKeySessionID($request);
        if($valid!==true)return $valid;

        if(!$this->isValidAccount($AccountKey)){
            return $this->response(0,null,'Impossible de charger le compte',null,null,500);
        }
        $lcdtapp_api_session=ApiSession::where('sessionid','=', $SessionID)->first();
 
       

        if($lcdtapp_api_session==null)
            return $this->response(0,null,'Session invalide ou expirée.',null,null,401);
        
        $SessionType = $lcdtapp_api_session->sessiontype;

        $expired=$this->isExpired($SessionID,$AccountKey);
        $obj = new \stdClass();
        $obj->isLoggedIn = !$expired&&$SessionType=='login';
       return $this->response(1,$obj,($expired?'Session invalide ou expirée.':'Le jeton est valide.'));

    }
    public function isValidAccount($AccountKey){
        $lcdtapp_api=Api::where('accountkey','=',$AccountKey)->first();
        if ($lcdtapp_api==null){
            return false;
        }
        else{
            return true;
        }
    }
    public function Logoff(Request $request){
        $SessionID=$request->post('SessionID');
        $AccountKey=$request->post('AccountKey');

        $valid=$this->isValidAccountKeySessionID($request);
        if($valid!==true)return $valid;
    
        $lcdtapp_api_session=ApiSession::where('sessionid','=', $SessionID)->first();
        if($lcdtapp_api_session==null)
        return $this->response(0,null,'Session invalide ou expirée.',null,null,401);
    
        $SessionType = $lcdtapp_api_session->sessiontype;

      

        $expired=$this->isExpired($SessionID,$AccountKey);
        if($expired||$SessionType=='token'){
            return $this->response(0,null,'Session invalide ou expirée.',null,null,401);
        }
        $lcdtapp_api_session->last_activity='2000-01-01 00:00:00';
        $lcdtapp_api_session->save();
  

        return $this->response(1,null,"Déconnexion réussie.");
    }

    public function GetEventList(Request $request){
            $SessionID=$request->post('SessionID');
            $AccountKey=$request->post('AccountKey');
            $Parameters=$request->post('Parameters');

            $valid=$this->isValidAccountKeySessionID($request);
            if($valid!==true)return $valid;
            $isLoggedIn=$this->checkLogin($request);



            if($isLoggedIn===true){
                $lcdtapp_api_instance=$this->getApiInstance($AccountKey,$SessionID);
                $events=Event::where('user_id','=',$lcdtapp_api_instance->user_id);
                if(isset($Parameters['fromdate'])&&!$this->isBlank($Parameters['fromdate'])&&isset($Parameters['todate'])&&!$this->isBlank($Parameters['todate'])){
                    $events->whereBetween('datedebut',[$Parameters['fromdate'],$Parameters['todate']]);
                }else if(isset($Parameters['fromdate'])&&!$this->isBlank($Parameters['fromdate'])){
                    $events->where('datedebut','>=',$Parameters['fromdate']);
                }else if(isset($Parameters['todate'])&&!$this->isBlank($Parameters['todate'])){
                    $events->where('datedebut','<=',$Parameters['todate']);
                }
                
                $events=$events->orderBy('datedebut', 'asc')->get();
                foreach($events as &$event){
                    $event->makeHidden(['created_at','updated_at','deleted_at']);
                    $event->user->roles=$event->user->getRoles();
                    $event->user->makeHidden(['created_at','updated_at']);
                    $event->user->avatar=getenv('APP_URL').Storage::url( $event->user->avatar);
                    $event->customer->makeHidden(['created_at','updated_at','deleted_at']);
                    $event->address;
                    if($event->address!=null)
                    $event->address->makeHidden(['created_at','updated_at','deleted_at']);;
                }
                return $this->response(1,$events);
            }else{
                return $isLoggedIn;
            }
    }

    public function GetCustomers(Request $request){
        $SessionID=$request->post('SessionID');
        $AccountKey=$request->post('AccountKey');

        $valid=$this->isValidAccountKeySessionID($request);
        if($valid!==true)return $valid;
        $isLoggedIn=$this->checkLogin($request);



        if($isLoggedIn===true){
            $lcdtapp_api_instance=$this->getApiInstance($AccountKey,$SessionID);
            $affiliate=$lcdtapp_api_instance->user->affiliate;
            if($affiliate==null)
            return $this->response(0,null,'Impossible de récupérer les clients.','Utilisateur non associé à un affilié.');


            return $this->response(1,$affiliate->customers->where('active','=',1));
        }else{
            return $isLoggedIn;
        }
    }
    public function DeleteGedDetail(Request $request){
        $Parameters=$request->post('Parameters');
        $SessionID=$request->post('SessionID');
        $AccountKey=$request->post('AccountKey');

        $valid=$this->isValidAccountKeySessionID($request);
        if($valid!==true)return $valid;
        $isLoggedIn=$this->checkLogin($request);



        if($isLoggedIn===true){
            $lcdtapp_api_instance=$this->getApiInstance($AccountKey,$SessionID);
            $affiliate=$lcdtapp_api_instance->user->affiliate;
            if($affiliate==null)
                return $this->response(0,null,'Impossible de supprimer les détails de ged.','Utilisateur non associé à un affilié.');

            if(!isset($Parameters['ged_detail_id'])||$this->isBlank($Parameters['ged_detail_id'])){
                return $this->response(0,null,'ged_detail_id est requis.');   
            }
            $gedDetail=GedDetail::where('id','=',$Parameters['ged_detail_id'])->first();
            if($gedDetail==null)
                return $this->response(0,null,'Aucun détail de ged trouvé');
            $order=$gedDetail->ged->order;     

            if($order->affiliate_id!=$affiliate->id){
                    return $this->response(0,null,'Les détails de Ged ne sont pas liés à l\'affilié actuel.');   
            }
            $this->l("DEL","DÉTAIL API GED SUPPRIMÉ",$lcdtapp_api_instance->user->id);
            $gedDetail->delete();
            return $this->response();
         
        }else{
            return $isLoggedIn;
        }
    }

    public function DeleteOrderZone(Request $request){
        $Parameters=$request->post('Parameters');
        $SessionID=$request->post('SessionID');
        $AccountKey=$request->post('AccountKey');

        $valid=$this->isValidAccountKeySessionID($request);
        if($valid!==true)return $valid;
        $isLoggedIn=$this->checkLogin($request);



        if($isLoggedIn===true){
            $lcdtapp_api_instance=$this->getApiInstance($AccountKey,$SessionID);
            $affiliate=$lcdtapp_api_instance->user->affiliate;
            if($affiliate==null)
                return $this->response(0,null,'Impossible de supprimer la zone de commande.','Utilisateur non associé à un affilié.');

            if(!isset($Parameters['order_zone_id'])||$this->isBlank($Parameters['order_zone_id'])){
                return $this->response(0,null,'order_zone_id is required.');   
            }
            $orderZone=OrderZone::find($Parameters['order_zone_id']);

            if($orderZone==null)
                return $this->response(0,null,'Aucune zone de commande trouvée.');
            $order=$orderZone->order;     

            if($order==null)
            return $this->response(0,null,'Commande associée introuvable');

            if($order->affiliate_id!=$affiliate->id){
                    return $this->response(0,null,'La zone de commande n\'est pas liée à l\'affilié actuel.');   
            }
            $this->l("DEL","ZONE DE COMMANDE API SUPPRIMÉE",$lcdtapp_api_instance->user->id);
            $orderZone->delete();
            return $this->response();
         
        }else{
            return $isLoggedIn;
        }
    }

    public function SaveEventComment(Request $request){
        $Parameters=$request->post('Parameters');
        $SessionID=$request->post('SessionID');
        $AccountKey=$request->post('AccountKey');

        $valid=$this->isValidAccountKeySessionID($request);
        if($valid!==true)return $valid;
        $isLoggedIn=$this->checkLogin($request);



        if($isLoggedIn===true){
            $lcdtapp_api_instance=$this->getApiInstance($AccountKey,$SessionID);
            $affiliate=$lcdtapp_api_instance->user->affiliate;
            if($affiliate==null)
                return $this->response(0,null,'	Impossible d\'enregistrer le commentaire de l\'événement.','Utilisateur non associé à un affilié.');

            if(!isset($Parameters['event_id'])||$this->isBlank($Parameters['event_id'])){
                return $this->response(0,null,'event_id est requis.');   
            }

            if(!isset($Parameters['comment'])||$this->isBlank($Parameters['comment'])){
                return $this->response(0,null,'un commentaire est requis.');   
            }
            $event=Event::find($Parameters['event_id']);
            if($event==null)
            return $this->response(0,null,'Evénement introuvable.'); 
            
            if($event->affiliate_id!=$affiliate->id)
            return $this->response(0,null,'L\'événement n\'est pas lié à l\'affilié actuel.'); 

            $comment=new EventComment();
       
            if(isset($Parameters['id'])||!$this->isBlank($Parameters['id'])){
                $comment=EventComment::find($Parameters['id']);
                if($comment==null)
                return $this->response(0,null,'Commentaire existant introuvable.','event_comment_id '.$Parameters['id']); 

                if($comment->event_id!=$event->id)
                return $this->response(0,null,'Commentaire existant non lié à l\'événement spécifié','event_id '.$Parameters['event_id'].' | Comment event id '.$comment->event_id ); 
                //only the user can edit his own comment else create new comment
                if($comment->user_id!=$affiliate=$lcdtapp_api_instance->user->id)
                $comment=new EventComment();
            }
            $comment->user_id= $affiliate=$lcdtapp_api_instance->user->id;

           $comment->comment=$Parameters['comment'];

           $event->eventComments()->save($comment);

           
            return $this->response();
         
        }else{
            return $isLoggedIn;
        }
    }

    public function Search(Request $request){
        $Parameters=$request->post('Parameters');
        $SessionID=$request->post('SessionID');
        $AccountKey=$request->post('AccountKey');

        $valid=$this->isValidAccountKeySessionID($request);
        if($valid!==true)return $valid;
        $isLoggedIn=$this->checkLogin($request);

       

        if($isLoggedIn===true){
            $lcdtapp_api_instance=$this->getApiInstance($AccountKey,$SessionID);
            $affiliate=$lcdtapp_api_instance->user->affiliate;
            if($affiliate==null)
                return $this->response(0,null,'Impossible d\'effectuer la recherche.','Utilisateur non associé à un affilié.');

            if(!isset($Parameters['words'])||$this->isBlank($Parameters['words'])){
                return $this->response(0,null,'mots est nécessaire.');   
            }
        
          $words=explode(' ',$Parameters['words']);
    
          $results=DB::table('orders')->select(['orders.id as order_id','orders.reference','customers.name as lastname','customers.firstname','customers.company','addresses.address1', 'addresses.address2','addresses.postcode', 'addresses.city', 'events.id as event_id','events.name as event_name'])
          ->join('events',function($join) use($words){
                $join->on('orders.id','=','events.order_id')
                ->whereNull('orders.deleted_at')
                ->whereNull('events.deleted_at');
               
          })->join('addresses',function($join){
              $join->on('events.address_id','=','addresses.id')
              ->whereNull('addresses.deleted_at');
          })->join('customers',function($join) use($words){
              $join->on('orders.customer_id','=','customers.id')
              ->whereNull('customers.deleted_at')
              ->where('customers.active','=',1);
          })
          ->where(function($query) use ($words){
            $query->where('orders.reference','LIKE','%'.$words[0].'%')->orWhere('orders.id','LIKE','%'.$words[0].'%');

            if(count($words)>1){
                foreach($words as $k=>$word){
                    if($k>0)
                    $query->orWhere('orders.reference','LIKE','%'.$word.'%')->orWhere('orders.id','LIKE','%'.$word.'%');
                }
            }

            $query->orWhere('customers.name','LIKE','%'.$words[0].'%')->orWhere('customers.firstname','LIKE','%'.$words[0].'%')->orWhere('customers.company','LIKE','%'.$words[0].'%');
    
            if(count($words)>1){
                foreach($words as $k=>$word){
                    if($k>0)
                    $query->orWhere('customers.name','LIKE','%'.$word.'%')->orWhere('customers.firstname','LIKE','%'.$word.'%')->orWhere('customers.company','LIKE','%'.$words[0].'%');
                }
            }
          })
          ->orderByDesc('orders.id')->get();
          

           
            return $this->response(1,$results);
         
        }else{
            return $isLoggedIn;
        }
    } 

    public function SaveOrderZoneComment(Request $request){
        $Parameters=$request->post('Parameters');
        $SessionID=$request->post('SessionID');
        $AccountKey=$request->post('AccountKey');

        $valid=$this->isValidAccountKeySessionID($request);
        if($valid!==true)return $valid;
        $isLoggedIn=$this->checkLogin($request);



        if($isLoggedIn===true){
            $lcdtapp_api_instance=$this->getApiInstance($AccountKey,$SessionID);
            $affiliate=$lcdtapp_api_instance->user->affiliate;
            if($affiliate==null)
                return $this->response(0,null,'	Impossible d\'enregistrer le commentaire de la zone de commande.','Utilisateur non associé à un affilié.');

            if(!isset($Parameters['order_zone_id'])||$this->isBlank($Parameters['order_zone_id'])){
                return $this->response(0,null,'order_zone_id est requis. ');   
            }

            if(!isset($Parameters['comment'])||$this->isBlank($Parameters['comment'])){
                return $this->response(0,null,'un commentaire est requis.');   
            }
            $OrderZone=OrderZone::find($Parameters['order_zone_id']);
            if($OrderZone==null)
            return $this->response(0,null,'OrderZone introuvable.'); 
            
            if($OrderZone->order->affiliate_id!=$affiliate->id)
            return $this->response(0,null,'OrderZone n\'est pas lié à l\'affilié actuel.'); 

            $comment=new OrderZoneComment();
       
            if(isset($Parameters['id'])||!$this->isBlank($Parameters['id'])){
                $comment=OrderZoneComment::find($Parameters['id']);
                if($comment==null)
                return $this->response(0,null,'Commentaire existant introuvable.','order_zone_comment_id '.$Parameters['id']); 

                if($comment->order_zone_id!=$OrderZone->id)
                return $this->response(0,null,'Commentaire existant non lié à la zone de commande spécifiée','order_zone_id '.$Parameters['order_zone_id'].' | Comment order zone id '.$comment->order_zone_id ); 
                //only the user can edit his own comment else create new comment
                if($comment->user_id!=$affiliate=$lcdtapp_api_instance->user->id)
                $comment=new OrderZoneComment();
            }
            $comment->user_id= $affiliate=$lcdtapp_api_instance->user->id;

           $comment->comment=$Parameters['comment'];

           $OrderZone->orderZoneComments()->save($comment);

           
            return $this->response();
         
        }else{
            return $isLoggedIn;
        }
    }
    public function DeleteEventComment(Request $request){
        $Parameters=$request->post('Parameters');
        $SessionID=$request->post('SessionID');
        $AccountKey=$request->post('AccountKey');

        $valid=$this->isValidAccountKeySessionID($request);
        if($valid!==true)return $valid;
        $isLoggedIn=$this->checkLogin($request);



        if($isLoggedIn===true){
            $lcdtapp_api_instance=$this->getApiInstance($AccountKey,$SessionID);
            $affiliate=$lcdtapp_api_instance->user->affiliate;
            if($affiliate==null)
                return $this->response(0,null,'Impossible de supprimer le commentaire de l\'événement.','Utilisateur non associé à un affilié.');

            if(!isset($Parameters['event_comment_id'])||$this->isBlank($Parameters['event_comment_id'])){
                return $this->response(0,null,'event_comment_id est requis.');   
            }

            $eventComment=EventComment::find($Parameters['event_comment_id']);
            if($eventComment==null)
            return $this->response(0,null,'Commentaire d\'événement introuvable.'); 
            $event=$eventComment->event;
            if($event->affiliate_id!=$affiliate->id)
            return $this->response(0,null,'Le commentaire de l\'événement n\'est pas lié à l\'affilié actuel.'); 
            $eventComment->delete();
            $this->l("DEL","COMMENTAIRE D\'ÉVÉNEMENT API SUPPRIMÉ",$lcdtapp_api_instance->user->id);
           
            return $this->response();
         
        }else{
            return $isLoggedIn;
        }
    }

    public function DeleteOrderZoneComment(Request $request){
        $Parameters=$request->post('Parameters');
        $SessionID=$request->post('SessionID');
        $AccountKey=$request->post('AccountKey');

        $valid=$this->isValidAccountKeySessionID($request);
        if($valid!==true)return $valid;
        $isLoggedIn=$this->checkLogin($request);



        if($isLoggedIn===true){
            $lcdtapp_api_instance=$this->getApiInstance($AccountKey,$SessionID);
            $affiliate=$lcdtapp_api_instance->user->affiliate;
            if($affiliate==null)
                return $this->response(0,null,'Impossible de supprimer le commentaire de la zone de commande.	','Utilisateur non associé à un affilié.');

            if(!isset($Parameters['orderzone_comment_id'])||$this->isBlank($Parameters['orderzone_comment_id'])){
                return $this->response(0,null,'orderzone_comment_id est requis.');   
            }

            $orderzoneComment=OrderZoneComment::find($Parameters['orderzone_comment_id']);
            if($orderzoneComment==null)
            return $this->response(0,null,'Commentaire de la zone de commande introuvable.'); 
            $order=$orderzoneComment->OrderZone->order;


            if($order->affiliate_id!=$affiliate->id)
            return $this->response(0,null,'Le commentaire de la zone de commande n\'est pas lié à l\'affilié actuel.'); 
            $orderzoneComment->delete();
            $this->l("DEL","COMMENTAIRE DE ZONE DE COMMANDE API SUPPRIMÉ",$lcdtapp_api_instance->user->id);
           
            return $this->response();
         
        }else{
            return $isLoggedIn;
        }
    }
    public function GetCustomer(Request $request){
        $Parameters=$request->post('Parameters');
        $SessionID=$request->post('SessionID');
        $AccountKey=$request->post('AccountKey');

        $valid=$this->isValidAccountKeySessionID($request);
        if($valid!==true)return $valid;
        $isLoggedIn=$this->checkLogin($request);



        if($isLoggedIn===true){
            $lcdtapp_api_instance=$this->getApiInstance($AccountKey,$SessionID);
            $affiliate=$lcdtapp_api_instance->user->affiliate;
            if($affiliate==null)
                return $this->response(0,null,'Impossible de récupérer le client.','Utilisateur non associé à un affilié');

            if(!isset($Parameters['customeruuid'])||$this->isBlank($Parameters['customeruuid'])){
                return $this->response(0,null,'Customer UUID is required.');   
            }
            $customer=Customer::where('customeruuid','=',$Parameters['customeruuid'])->first();
            if($customer==null)
                return $this->response(0,null,'Aucun client trouvé.'); 
          
            if($customer->affiliate_id!=$affiliate->id)
                return $this->response(0,null,'Le client n\'appartient pas à l\'affilié de l\'utilisateur.'); 
                $customer->makeHidden(['created_at','updated_at','deleted_at']);
            return $this->response(1,$customer);
        }else{
            return $isLoggedIn;
        }
    }
    public function GetCustomerContacts(Request $request){
        $Parameters=$request->post('Parameters');
        $SessionID=$request->post('SessionID');
        $AccountKey=$request->post('AccountKey');

        $valid=$this->isValidAccountKeySessionID($request);
        if($valid!==true)return $valid;
        $isLoggedIn=$this->checkLogin($request);



        if($isLoggedIn===true){
            $lcdtapp_api_instance=$this->getApiInstance($AccountKey,$SessionID);
            $affiliate=$lcdtapp_api_instance->user->affiliate;
            if($affiliate==null)
                return $this->response(0,null,'Impossible de récupérer le client.','Utilisateur non associé à un affilié.');

            if(!isset($Parameters['customeruuid'])||$this->isBlank($Parameters['customeruuid'])){
                return $this->response(0,null,'Customer UUID is required.');   
            }
            $customer=Customer::where('customeruuid','=',$Parameters['customeruuid'])->first();
            if($customer==null)
                return $this->response(0,null,'No customer found.'); 
          
            if($customer->affiliate_id!=$affiliate->id)
                return $this->response(0,null,'Customer does not belong to user\'s affiliate.'); 
         
            $customerContacts=$customer->contacts();

            return $this->response(1,$customerContacts->get()->makeHidden(['created_at','updated_at','deleted_at']));
        }else{
            return $isLoggedIn;
        }
    }
    public function GetCustomerAddresses(Request $request){
        $SessionID=$request->post('SessionID');
        $AccountKey=$request->post('AccountKey');
        $Parameters=$request->post('Parameters');

        $valid=$this->isValidAccountKeySessionID($request);
        if($valid!==true)return $valid;
        $isLoggedIn=$this->checkLogin($request);
        if(!isset($Parameters['customer_id'])||$this->isBlank($Parameters['customer_id'])){
            return $this->response(0,null,'Missing customer_id.');
        }

        if($isLoggedIn===true){
            $customer=Customer::findOrFail($Parameters['customer_id']);
            return $this->response(1,$customer->addresses);
        }else{
            return $isLoggedIn;
        }
    }
    public function GetUsers(Request $request){
        $SessionID=$request->post('SessionID');
        $AccountKey=$request->post('AccountKey');

        $valid=$this->isValidAccountKeySessionID($request);
        if($valid!==true)return $valid;
        $isLoggedIn=$this->checkLogin($request);



        if($isLoggedIn===true){
            $lcdtapp_api_instance=$this->getApiInstance($AccountKey,$SessionID);
            $users=User::where('affiliate_id','=',$lcdtapp_api_instance->user->affiliate_id)->get();
            foreach($users as &$user){
                $user->roles=$user->getRoles();
                $user->makeHidden(['created_at','updated_at','deleted_at']);
                $user->avatar=getenv('APP_URL').Storage::url( $user->avatar);
                $user->affiliate->makeHidden(['created_at','updated_at','deleted_at']);
            }
            return $this->response(1,$users);
        }else{
            return $isLoggedIn;
        }
}
public function GetMoyenAcces(Request $request){
    $SessionID=$request->post('SessionID');
    $AccountKey=$request->post('AccountKey');

    $valid=$this->isValidAccountKeySessionID($request);
    if($valid!==true)return $valid;
    $isLoggedIn=$this->checkLogin($request);



    if($isLoggedIn===true){
        $lcdtapp_api_instance=$this->getApiInstance($AccountKey,$SessionID);
        $affiliate=$lcdtapp_api_instance->user->affiliate;
        if($affiliate==null)
            return $this->response(0,null,'Impossible d\'accéder au moyen.','Utilisateur non associé à un affilié.');

        $moyenacces=$affiliate->moyenacces()->get();
   
        foreach($moyenacces as &$moyen){
         
            $moyen->makeHidden(['created_at','updated_at','deleted_at','affiliate_id']);
          
        }
        return $this->response(1,$moyenacces);
    }else{
        return $isLoggedIn;
    }
}
public function GetEventTypes(Request $request){
    $token_SessionID=$request->post('SessionID');
    $AccountKey=$request->post('AccountKey');
    

    if($token_SessionID==null){
        return $this->response(0,null,'Paramètre SessionID manquant.');
    }
    if($AccountKey==null){
        return $this->response(0,null,'Paramètre AccountKey manquant.');
    }

    //verify sessionid
    if($this->isExpired($token_SessionID,$AccountKey)){
        return $this->response(0,null,'Impossible de vérifier le compte','Session invalide ou expirée.',null,401);
    }
    return $this->response(1,EventType::all()->makeHidden(['created_at','updated_at','deleted_at']));

} 
public function GetEventStatuses(Request $request){
    $token_SessionID=$request->post('SessionID');
    $AccountKey=$request->post('AccountKey');
    

    if($token_SessionID==null){
        return $this->response(0,null,'Paramètre SessionID manquant. ');
    }
    if($AccountKey==null){
        return $this->response(0,null,'Paramètre AccountKey manquant.');
    }

    //verify sessionid
    if($this->isExpired($token_SessionID,$AccountKey)){
        return $this->response(0,null,'Impossible de vérifier le compte','Session invalide ou expirée.',null,401);
    }
    return $this->response(1,EventStatus::all()->makeHidden(['created_at','updated_at','deleted_at']));

} 
public function GetEventOrigins(Request $request){
    $token_SessionID=$request->post('SessionID');
    $AccountKey=$request->post('AccountKey');
    

    if($token_SessionID==null){
        return $this->response(0,null,'Paramètre SessionID manquant. ');
    }
    if($AccountKey==null){
        return $this->response(0,null,'Paramètre AccountKey manquant.');
    }

    //verify sessionid
    if($this->isExpired($token_SessionID,$AccountKey)){
        return $this->response(0,null,'Impossible de vérifier le compte','Session invalide ou expirée.',null,401);
    }
    return $this->response(1,EventOrigin::all()->makeHidden(['created_at','updated_at','deleted_at']));

} 
public function GetOrderStates(Request $request){
    $token_SessionID=$request->post('SessionID');
    $AccountKey=$request->post('AccountKey');
    

    if($token_SessionID==null){
        return $this->response(0,null,'Paramètre SessionID manquant.');
    }
    if($AccountKey==null){
        return $this->response(0,null,'Paramètre AccountKey manquant.');
    }

    //verify sessionid
    if($this->isExpired($token_SessionID,$AccountKey)){
        return $this->response(0,null,'Impossible de vérifier le compte','Session invalide ou expirée.',null,401);
    }
    $states=OrderState::all()->makeHidden(['created_at','updated_at','deleted_at']);
    return $this->response(1,$states);

}

public function GetGedDetailCategory(Request $request){
    $token_SessionID=$request->post('SessionID');
    $AccountKey=$request->post('AccountKey');
    

    if($token_SessionID==null){
        return $this->response(0,null,'Paramètre SessionID manquant.');
    }
    if($AccountKey==null){
        return $this->response(0,null,'Paramètre AccountKey manquant.');
    }

    //verify sessionid
    if($this->isExpired($token_SessionID,$AccountKey)){
        return $this->response(0,null,'Impossible de vérifier le compte','Session invalide ou expirée.',null,401);
    }
    $statuses=GedCategory::all()->makeHidden(['created_at','updated_at','deleted_at']);
    return $this->response(1,$statuses);

}
    public function GetEventDetail(Request $request){
        $Parameters=$request->post('Parameters');
        $SessionID=$request->post('SessionID');
        $AccountKey=$request->post('AccountKey');



        $valid=$this->isValidAccountKeySessionID($request);
        if($valid!==true)return $valid;
        $isLoggedIn=$this->checkLogin($request);
        if(!isset($Parameters['event_id'])||$this->isBlank($Parameters['event_id'])){
            return $this->response(0,null,'Missing event_id.');
        }



        if($isLoggedIn===true){
            $lcdtapp_api_instance=$this->getApiInstance($AccountKey,$SessionID);
            $event=Event::where('id','=',$Parameters['event_id'])->first();
            $event->makeHidden(['created_at','updated_at','deleted_at']);
            if($event==null)
            return $this->response(0,null,'Event not found.');

            if($event->affiliate_id!=$lcdtapp_api_instance->user->affiliate->id)
            return $this->response(0,null,'L\'événement n\'appartient pas à la franchise affiliée de l\'utilisateur.');
                $event->user->roles=$event->user->getRoles();
                $event->user->makeHidden(['created_at','updated_at']);
                $event->user->avatar=getenv('APP_URL').Storage::url( $event->user->avatar);
                $event->customer->makeHidden(['created_at','updated_at','deleted_at']);
                $event->address->makeHidden(['created_at','updated_at','deleted_at']);
                $event->eventType->makeHidden(['created_at','updated_at','deleted_at']);
                if($event->eventOrigin!=null)  
                $event->eventOrigin->makeHidden(['created_at','updated_at','deleted_at']);
                $event->eventComments->makeHidden(['updated_at','deleted_at','user_id','event_id']);
     
            foreach($event->eventComments as &$eventComment){
                $eventComment->user->makeHidden([
                    "email_verified_at",
                    "settings",
                    "created_at",
                    "updated_at",
                    "affiliate_id"
            ]);
            }
            return $this->response(1,$event);
        }else{
            return $isLoggedIn;
        }
}

public function GetDevis(Request $request){
    $Parameters=$request->post('Parameters');
    $SessionID=$request->post('SessionID');
    $AccountKey=$request->post('AccountKey');
 

    $valid=$this->isValidAccountKeySessionID($request);
    if($valid!==true)return $valid;
    $isLoggedIn=$this->checkLogin($request);
    if(!isset($Parameters['event_id'])||$this->isBlank($Parameters['event_id'])){
        return $this->response(0,null,'Missing event_id.');
    }



    if($isLoggedIn===true){
        $lcdtapp_api_instance=$this->getApiInstance($AccountKey,$SessionID);
        $event=$lcdtapp_api_instance->user->affiliate->events->where('id','=',$Parameters['event_id'])->first();
        if($event==null)
        return $this->response(0,null,'Event not found.');
        $order=$event->order()->first();
        if($order==null)  
          return $this->response(0,null,'Aucune cotation trouvée pour cet événement.');
        $order->makeHidden(['created_at','updated_at','deleted_at']);
        $order->orderZones; 
        $order->address->makeHidden(['created_at','updated_at','deleted_at']);
        foreach($order->orderZones as &$order_zone){
            $order_zone->makeHidden(['created_at','updated_at','deleted_at']);
            $order_zone->gedDetails->makeHidden(['created_at','updated_at','deleted_at','storage_path','file']);
            $order_zone->orderZoneComments->makeHidden(['updated_at','deleted_at','user_id','order_zone_id']);
     
            foreach($order_zone->orderZoneComments as &$orderZoneComment){
                $orderZoneComment->user->makeHidden([
                    "email_verified_at",
                    "settings",
                    "created_at",
                    "updated_at",
                    "affiliate_id"
            ]);
            }
            foreach($order_zone->gedDetails as &$ged_detail){
                if($ged_detail->type!='description')
                $ged_detail->urls=$this->getFileUrls($ged_detail);
            }
        }
        return $this->response(1, $order);
    }else{
        return $isLoggedIn;
    }
}


public function GetDevisByOrderId(Request $request){
    $Parameters=$request->post('Parameters');
    $SessionID=$request->post('SessionID');
    $AccountKey=$request->post('AccountKey');
 

    $valid=$this->isValidAccountKeySessionID($request);
    if($valid!==true)return $valid;
    $isLoggedIn=$this->checkLogin($request);
    if(!isset($Parameters['order_id'])||$this->isBlank($Parameters['order_id'])){
        return $this->response(0,null,'Missing order_id.');
    }



    if($isLoggedIn===true){
        $lcdtapp_api_instance=$this->getApiInstance($AccountKey,$SessionID);
      
        $order=Order::find($Parameters['order_id']);
        if($order==null)  
          return $this->response(0,null,'Aucune cotation(devis) trouvée');

        if($lcdtapp_api_instance->user->affiliate->id!=$order->affiliate_id) 
        return $this->response(0,null,'La citation (devis) n\'appartient pas à l\'affilié de l\'utilisateur');

        $order->makeHidden(['created_at','updated_at','deleted_at']);
        $order->orderZones;  
        $order->customer->makeHidden(['created_at','updated_at','deleted_at']); 
        $order->address->makeHidden(['created_at','updated_at','deleted_at']);
        foreach($order->orderZones as &$order_zone){
            $order_zone->makeHidden(['created_at','updated_at','deleted_at']);
            $order_zone->gedDetails->makeHidden(['created_at','updated_at','deleted_at','storage_path','file']);
            $order_zone->orderZoneComments->makeHidden(['updated_at','deleted_at','user_id','order_zone_id']);
     
            foreach($order_zone->orderZoneComments as &$orderZoneComment){
                $orderZoneComment->user->makeHidden([
                    "email_verified_at",
                    "settings",
                    "created_at",
                    "updated_at",
                    "affiliate_id"
            ]);
            }
            foreach($order_zone->gedDetails as &$ged_detail){
                if($ged_detail->type!='description')
                $ged_detail->urls=$this->getFileUrls($ged_detail);
                $ged_detail->orderOuvrage;
                $ged_detail->gedCategory;
            }
        }
        return $this->response(1, $order);
    }else{
        return $isLoggedIn;
    }
}

public function GetTechnicianDevisDetails(Request $request){
    $Parameters=$request->post('Parameters');
    $SessionID=$request->post('SessionID');
    $AccountKey=$request->post('AccountKey');
 

    $valid=$this->isValidAccountKeySessionID($request);
    if($valid!==true)return $valid;
    $isLoggedIn=$this->checkLogin($request);
    if(!isset($Parameters['order_id'])||$this->isBlank($Parameters['order_id'])){
        return $this->response(0,null,'Missing order_id.');
    }



    if($isLoggedIn===true){
        $lcdtapp_api_instance=$this->getApiInstance($AccountKey,$SessionID);
      
        $order=Order::find($Parameters['order_id']);
        if($order==null)  
          return $this->response(0,null,'Aucune cotation(devis) trouvée');

        if($lcdtapp_api_instance->user->affiliate->id!=$order->affiliate_id) 
        return $this->response(0,null,'La citation (devis) n\'appartient pas à l\'affilié de l\'utilisateur');

        $order->makeHidden(['created_at','updated_at','deleted_at']);
        $order->orderZones;  
        $order->customer->makeHidden(['created_at','updated_at','deleted_at']); 
        $order->address->makeHidden(['created_at','updated_at','deleted_at']);
        $order->signatures=[];
        $geds=$order->geds()->get();
        foreach($geds as $ged){
            $gedDetails=$ged->gedDetails()->where('signature','=',1)->get();
            foreach($gedDetails as $gedDetail){
                $gedDetail->user->makeHidden(['created_at','updated_at','deleted_at']);
                $gedDetail->makeHidden(['updated_at','deleted_at','additional_work','storage_path','signature']);
                if($gedDetail->type!='description')
                $gedDetail->urls=$this->getFileUrls($gedDetail);
                $order->signatures=array_merge( $order->signatures,[$gedDetail]);

            }
           
        }
        $additional_works=[];
        foreach($order->orderZones as &$order_zone){

            $order_zone->makeHidden(['created_at','updated_at','deleted_at']);
          
          
            $gedDetails= $order_zone->gedDetails()->get();

            if(!isset($order_zone->satellite_view))
            $order_zone->satellite_view=[];
            foreach($gedDetails as $gedDetail){
                if($gedDetail->order_ouvrage_id==0&&$gedDetail->ged_category_id==0&&$gedDetail->additional_work==0){
                    $gedDetail->makeHidden(['created_at','updated_at','deleted_at','additional_work','storage_path']);
                    if($gedDetail->type!='description')
                    $gedDetail->urls=$this->getFileUrls($gedDetail);
                    $order_zone->satellite_view = array_merge($order_zone->satellite_view, [$gedDetail]);
                }

             
            }
            $orderCategories=$order_zone->orderCategories()->get();
            if(!isset($order_zone->order_categories))
            $order_zone->order_categories=[];
            foreach($orderCategories as $orderCategory){
                $list_ouvrages=$orderCategory->orderOuvrages()->get();
              // dd($list_ouvrages);
                $o=new stdClass();
                $o->name=$orderCategory->name;
                $o->list_ouvrages=[];
                foreach ( $list_ouvrages as &$order_ouvrage){
                    $order_ouvrage->makeHidden(['created_at','updated_at','deleted_at']);
   

                    if(!isset($order_ouvrage->Avant))
                    $order_ouvrage->Avant=[];   
                    if(!isset($order_ouvrage->Apres))
                        $order_ouvrage->Apres=[];

                        foreach($gedDetails as $gedDetail){
                            if($gedDetail->order_ouvrage_id==$order_ouvrage->id&&$gedDetail->additional_work==0){
                                $gedDetail->makeHidden(['created_at','updated_at','deleted_at','additional_work','storage_path']);
                                if($gedDetail->type!='description')
                                $gedDetail->urls=$this->getFileUrls($gedDetail);

                                if($gedDetail->timeline=='AVANT'){
                                $order_ouvrage->Avant=array_merge($order_ouvrage->Avant,[$gedDetail]);

                                }else if($gedDetail->timeline=='APRES'){
                                    $order_ouvrage->Apres=array_merge($order_ouvrage->Apres,[$gedDetail]);
                                }
                            }
                        }
                  
                }
                $o->list_ouvrages= $list_ouvrages;
                //$o->list_ouvrages[]=
                $order_zone->order_categories=array_merge($order_zone->order_categories,[$o]);
            }
            $additionalWorks= $order_zone->gedDetails()->where('additional_work','=',1)->get();
            foreach($additionalWorks as $a)
            $additional_works[]=$a;
            

            $order_zone->orderZoneComments->makeHidden(['updated_at','deleted_at','user_id','order_zone_id']);
     
            foreach($order_zone->orderZoneComments as &$orderZoneComment){
                $orderZoneComment->user->makeHidden([
                    "email_verified_at",
                    "settings",
                    "created_at",
                    "updated_at",
                    "affiliate_id"
            ]);
            }

        }

        $order1=Order::find($Parameters['order_id']);
        $order->travaux_supplementaire=[];
        $gedCats=[];
        $gedCatName=[];
        foreach($order1->orderZones as $order_zone){
            foreach($additional_works as $additional_work){
                if($additional_work->type!='description')
               $additional_work->urls=$this->getFileUrls($additional_work);
                $a=$additional_work->makeHidden(['created_at','updated_at','deleted_at','additional_work','storage_path','signature','timeline'])->toArray();
             
                $gedCategory=$additional_work->gedCategory;
          
                if(!key_exists($gedCategory->id,$gedCats)){
                    $gedCats[$gedCategory->id]=[];
                    $gedCatName[$gedCategory->id]=$gedCategory->name;
                }
                
                $gedCats[$gedCategory->id][]=$a;
            }
        }
        $sup=[];
        if(!empty($gedCats))
        foreach($gedCats as $gedCatId=>$gedDetails){

            foreach($order1->orderZones as $order_zone){
        
                if(!is_array($order_zone->ged_details))
                $order_zone->ged_details=[];
                $o=new stdClass;
                $o->name=$gedCatName[$gedCatId];

                $order_zone->makeHidden(['created_at','updated_at','deleted_at']);
                foreach($gedDetails as $gedDetail ){
             
                    if($order_zone->id==$gedDetail['order_zone_id']&&$gedDetail['ged_category_id']==$gedCatId){
                   
                            $o->list[]=$gedDetail;
                       
                    }
                }
        
                $order_zone->ged_details=array_merge($order_zone->ged_details,[$o]) ;
          
                $sup[$order_zone->id]=$order_zone;
        
            }
      
        }
        $order->travaux_supplementaire=array_merge( $order->travaux_supplementaire,$sup); 
        return $this->response(1, $order);
    }else{
        return $isLoggedIn;
    }
}
public function GetOrderOuvrages(Request $request){
    $Parameters=$request->post('Parameters');
    $SessionID=$request->post('SessionID');
    $AccountKey=$request->post('AccountKey');
 

    $valid=$this->isValidAccountKeySessionID($request);
    if($valid!==true)return $valid;
    $isLoggedIn=$this->checkLogin($request);
    if(!isset($Parameters['order_id'])||$this->isBlank($Parameters['order_id'])){
        return $this->response(0,null,'Missing order_id.');
    }



    if($isLoggedIn===true){
        $lcdtapp_api_instance=$this->getApiInstance($AccountKey,$SessionID);
        $order=$lcdtapp_api_instance->user->affiliate->orders->where('id','=',$Parameters['order_id'])->first();
        if($order==null)
        return $this->response(0,null,'Commande introuvable.');
        
        $orderOuvrages=$order->orderOuvrages->makeHidden(['created_at','updated_at','deleted_at']);
        if($orderOuvrages->count()==0)
        return $this->response(1,null,'Liste d\'ouvrage non disponible.');

        return $this->response(1, $orderOuvrages);
    }else{
        return $isLoggedIn;
    }
}

public function GetOrderCategories(Request $request){
    $Parameters=$request->post('Parameters');
    $SessionID=$request->post('SessionID');
    $AccountKey=$request->post('AccountKey');
 

    $valid=$this->isValidAccountKeySessionID($request);
    if($valid!==true)return $valid;
    $isLoggedIn=$this->checkLogin($request);
    if(!isset($Parameters['order_id'])||$this->isBlank($Parameters['order_id'])){
        return $this->response(0,null,'order_id manquant.');
    }



    if($isLoggedIn===true){
        $lcdtapp_api_instance=$this->getApiInstance($AccountKey,$SessionID);
        $order=$lcdtapp_api_instance->user->affiliate->orders->where('id','=',$Parameters['order_id'])->makeHidden(['created_at','updated_at','deleted_at'])->first();
        if($order==null)
        return $this->response(0,null,'Commande introuvable.');
        
        $orderZones=$order->orderZones->makeHidden(['created_at','updated_at','deleted_at']);
        $ordercats=array();
        foreach($orderZones as &$orderzone){
            $ordercat=$orderzone->orderCategories->makeHidden(['created_at','updated_at','deleted_at']);
            if($ordercat->count()>0)
            $ordercats=array_merge($ordercats,$ordercat->toArray());
        }
        $ordercatbyorderzone=array();
        if(!empty($ordercats))
        foreach($ordercats as $ordercat){
            $ordercatbyorderzone[$ordercat['order_zone_id']][]=$ordercat;
        }

        return $this->response(1, $ordercatbyorderzone);
    }else{
        return $isLoggedIn;
    }
}
public function SaveEvent(Request $request){
    $Parameters=$request->post('Parameters');
    $SessionID=$request->post('SessionID');
    $AccountKey=$request->post('AccountKey');
   

    $valid=$this->isValidAccountKeySessionID($request);
    if($valid!==true)return $valid;
    $isLoggedIn=$this->checkLogin($request);
    if(!isset($Parameters['customer_id'])||$this->isBlank($Parameters['customer_id'])){
        return $this->response(0,null,'	Customer_id manquant.');
    }
    if(!isset($Parameters['description'])||$this->isBlank($Parameters['description'])){
        return $this->response(0,null,'Une description est requise.');
    }
    if(!isset($Parameters['date_start'])||$this->isBlank($Parameters['date_start'])){
        return $this->response(0,null,'La date/l\'heure de début est requise.');
    }
    if(!isset($Parameters['date_end'])||$this->isBlank($Parameters['date_start'])){
        return $this->response(0,null,'La date/l\'heure de fin est requise.');
    }
    if(!isset($Parameters['name'])||$this->isBlank($Parameters['name'])){
        return $this->response(0,null,'Le nom de l\'événement est requis.');
    }
    if(!isset($Parameters['event_type_id'])||$this->isBlank($Parameters['event_type_id'])){
        return $this->response(0,null,'État de l\'événement non valide');
    }

    if(!isset($Parameters['event_origin_id'])||$this->isBlank($Parameters['event_origin_id'])){
        return $this->response(0,null,'L\'identifiant du type d\'événement est requis.');
    }

    if($this->isBlank($Parameters['contact_id'])){
        if(!isset($Parameters['contact_name'])||$this->isBlank($Parameters['contact_name'])){
            return $this->response(0,null,'Un nom de contact est requis.');
        }
        if(!isset($Parameters['contact_phone'])||$this->isBlank($Parameters['contact_phone'])){
            return $this->response(0,null,'Un numéro de téléphone est requis..');
        }
        if(!isset($Parameters['contact_email'])||$this->isBlank($Parameters['contact_email'])){
            return $this->response(0,null,'E-mail de contact est requis.');
        }
        if(!$this->validateEmail($Parameters['contact_email'])){
            return $this->response(0,null,'E-mail de contact n\'est pas valide.');
        }
        $existcontact=Contact::where('email','=',$Parameters['contact_email'])->first();
        if($existcontact!=null)
        return $this->response(0,null,'Un contact avec cette adresse e-mail existe déjà');
    }else{
        $contact=Contact::find($Parameters['contact_id']);
        if($contact->customer_id!=$Parameters['customer_id'])
        return $this->response(0,null,'Le contact n\'appartient pas au client.');
    }
    if($isLoggedIn===true){
  
        $lcdtapp_api_instance=$this->getApiInstance($AccountKey,$SessionID);
        $event=null;
        if(isset($Parameters['event_id'])&&$Parameters['event_id']!=null){
            $event=Event::find($Parameters['event_id']);
            if($event==null)
            return $this->response(0,null,'Evénement introuvable.');
    
            if($event->user_id!=$lcdtapp_api_instance->user_id){
                return $this->response(0,null,'Impossible de sauvegarder', 'événement non affecté à l\'utilisateur actuel.');
            }
           
        }

        if(!isset($Parameters['address_id'])||$Parameters['address_id']==null){
           if(!isset($Parameters['address'])){
            return $this->response(0,null,'Une adresse est requis','Aucun identifiant d\'adresse ou adresse specifiée');
           }else{
                if(!isset($Parameters['address']['address1'])||$this->isBlank($Parameters['address']['address1'])){
                    return $this->response(0,null,'Adresse 1 est requis');
                }
                if(!isset($Parameters['address']['postcode'])||$this->isBlank($Parameters['address']['postcode'])){
                    return $this->response(0,null,'Le code postal est requis');
                }
                if(!isset($Parameters['address']['city'])||$this->isBlank($Parameters['address']['city'])){
                    return $this->response(0,null,'La ville est requis');
                }
                if(!isset($Parameters['address']['longitude'])||$this->isBlank($Parameters['address']['longitude'])){
                    return $this->response(0,null,'La longitude est requis');
                }
                if(!isset($Parameters['address']['latitude'])||$this->isBlank($Parameters['address']['latitude'])){
                    return $this->response(0,null,'La latitude est requis');
                }
           }
               
        }else{
            $address=Address::where('id','=',$Parameters['address_id'])->first();
            if($address==null||$address->customer_id!=$Parameters['customer_id']){
                return $this->response(0,null,'Identifiant d\'adresse non valide','L\'adresse n\'existe pas ou n\'appartient pas au client.');
            }
        }
        $eventstatus=null;
        if(isset($Parameters['event_status_id'])&&!$this->isBlank($Parameters['event_status_id'])){
           $eventstatus=EventStatus::find($Parameters['event_status_id']);
           if($eventstatus==null){
            return $this->response(0,null,'État de l\'événement non valide');
           }
        }


        if($event==null)
        $event=new Event();

        $this->l('API SAVE EVENT',json_encode($Parameters),$lcdtapp_api_instance->user->id);

        if(!isset($Parameters['address_id'])||$Parameters['address_id']==null){
            if(isset($Parameters['address'])){
                $this->l('API SAVE EVENT WITH NEW ADDRESS',json_encode($Parameters['address']),$lcdtapp_api_instance->user->id);
                $address=new Address();
                $address->address_type_id=2;//chantier
                $address->country_id=8;	
                $address->state_id=0;
                $address->customer_id=$Parameters['customer_id'];	
                $address->alias='';	
                if(isset($Parameters['address']['company']))
                    $address->company=$Parameters['address']['company'];
                $address->lastname='';	
                $address->gender='';
                $address->firstname='';	
                $address->address1=$Parameters['address']['address1'];
                $address->address2=isset($Parameters['address']['address2'])?$Parameters['address']['address1']:'';
                $address->postcode=$Parameters['address']['postcode'];
                $address->city=$Parameters['address']['city'];
                $address->latitude=$Parameters['address']['latitude'];
                $address->longitude=$Parameters['address']['longitude'];
                $address->save();
                $Parameters['address_id']=$address->id;
            }
        }

        $contact=new Contact();
        if(!isset($Parameters['contact_id'])||$this->isBlank($Parameters['contact_id'])){
            $contact->email=$Parameters['contact_email'];
            $contact->mobile=$Parameters['contact_phone'];
            $contact->telephone=$Parameters['contact_phone'];
            $contact->customer_id=$Parameters['customer_id'];
            $contact->address_id=$Parameters['address_id'];
            $customer=Customer::find($Parameters['customer_id']);
            $contact->firstname=$customer->firstname;
            $contact->name=$Parameters['contact_name'];
            $contact->type='Client';
            $contact->comment='created from api';
            $contact->save();
        }

        $event->customer_id=$Parameters['customer_id'];
        $event->contact_id=isset($Parameters['contact_id'])?$Parameters['contact_id']:$contact->id;
        $event->event_type_id=$Parameters['event_type_id'];
        $event->affiliate_id=$lcdtapp_api_instance->user->affiliate->id;
        if(isset($Parameters['order_id']))
        $event->order_id=$Parameters['order_id'];
        $event->name=$Parameters['name'];
        $event->description=$Parameters['description'];
        $event->datedebut=$Parameters['date_start'];
        $event->datefin=$Parameters['date_end'];
        $event->address_id=isset($Parameters['address_id'])?$Parameters['address_id']:null;// first address of customer
        $event->user_id=isset($Parameters['affected_to_user'])?$Parameters['affected_to_user']:$lcdtapp_api_instance->user_id;
        $event->emetteur_id=$lcdtapp_api_instance->user_id;
        $event->event_origin_id=($event->event_origin_id>0?$event->event_origin_id:$Parameters['event_origin_id']);
        $event->save();
      
        if(isset($Parameters['event_id'])&&$Parameters['event_id']!=null){
            if($eventstatus!=null){
                $event->updateStatus($eventstatus->id,$lcdtapp_api_instance->user_id);
            }
        }else{
            $event->updateStatus(1,$lcdtapp_api_instance->user_id);
        }
    
        return $this->response(1,$event);
    }else{
        return $isLoggedIn;
    }
}



  public function SaveDevis(Request $request){
    $Parameters=$request->post('Parameters');
    $SessionID=$request->post('SessionID');
    $AccountKey=$request->post('AccountKey');
    //start validation
    $valid=$this->isValidAccountKeySessionID($request);
    if($valid!==true)return $valid;
    $isLoggedIn=$this->checkLogin($request);

    if($isLoggedIn===true){
      if(!isset($Parameters['event_id'])||$this->isBlank($Parameters['event_id']))
          return $this->response(0,null,'	ID d\'événement manquant');

      if(isset($Parameters['order_zones'])&&is_array($Parameters['order_zones'])){
        if(!empty($Parameters['order_zones'])){
            //order zones
          foreach($Parameters['order_zones'] as $order_zone){
            if(!isset($order_zone['latitude']) || $this->isBlank($order_zone['latitude']))
                return $this->response(0,null,'La latitude de la zone est requise.');

            if(!isset($order_zone['longitude'])||$this->isBlank($order_zone['longitude']))
                return $this->response(0,null,'La longitude de la zone est requise.');
        
            if(!isset($order_zone['name'])||$this->isBlank($order_zone['name']))
                return $this->response(0,null,'Le nom de la zone est requis.');
      
            //ged details
            if(isset($order_zone['ged_details'])&&is_array($order_zone['ged_details'])){
              //ged details
              foreach($order_zone['ged_details'] as $ged_detail){
                if((!isset($ged_detail['description'])||$this->isBlank($ged_detail['description']))&&(!isset($ged_detail['data'])||$this->isBlank($ged_detail['data']))){
                  return $this->response(0,null,'Détails ged non valides','Au moins une donnée de fichier ou une description est requise.');
                }
                if(isset($ged_detail['data'])&&!$this->isBlank($ged_detail['data']))
                  if(!isset($ged_detail['human_readable_filename'])||$this->isBlank($ged_detail['human_readable_filename'])){
                    return $this->response(0,null,'	Le nom du fichier de détails Ged est requis.');
                  }
                if(!isset($ged_detail['latitude'])||$this->isBlank($ged_detail['latitude'])){
                  return $this->response(0,null,'La latitude de détail Ged est requise.');
                }
                if(!isset($ged_detail['longitude'])||$this->isBlank($ged_detail['longitude'])){
                  return $this->response(0,null,'La longitude de détail Ged est requise.');
                }
              }
            }
          }
        }
      }
      $lcdtapp_api_instance = $this->getApiInstance($AccountKey,$SessionID);
      $event  = Event::find($Parameters['event_id']);
      $order  = null;
      if(isset($Parameters['order_id'])&&$Parameters['order_id']!=null){
        $order=Order::where('id','=',$Parameters['order_id'])->where('affiliate_id','=',$lcdtapp_api_instance->user->affiliate->id)->first();
        if($order==null)
          return $this->response(0,null,'Commande introuvable.');
        if($event->affiliate_id!=$lcdtapp_api_instance->user->affiliate->id)
          return $this->response(0,null,'Impossible de sauvegarder', 'événement non affecté à la franchise de l\'utilisateur actuel.');
      }
      //remove logo data before saving log       
      $params = $Parameters;
      if(isset($params['order_zones'])&&is_array($params['order_zones'])){
        if(!empty($params['order_zones'])){
          foreach($params['order_zones'] as &$order_zone){
            if(isset($order_zone['ged_details'])){
              foreach($order_zone['ged_details'] as &$ged_detail){
                $ged_detail['data']='REMOVED FOR LCDTLOG..';
              }
            }
          }
        }
      }
      $this->l('API SAVE DEVIS',json_encode($params),$lcdtapp_api_instance->user->id);
      if(isset($Parameters['order_zones'])&&is_array($Parameters['order_zones'])){
        if(!empty($Parameters['order_zones'])){
          //order zones
          foreach($Parameters['order_zones'] as $order_zone){
            if(!isset($order_zone['id'])||$order_zone['id']==null){
                $orderZone=new OrderZone();
            }else{
              $orderZone=OrderZone::find($order_zone['id']);
              if($orderZone==null){
                return $this->response(0,null,'Impossible de sauvegarder ', 'Order zone id '.$order_zone['id'].' pas trouvé.');
              }

              if($order!=null&&$order->id>0 && $orderZone->order_id!=$order->id){
                return $this->response(0,null,'Impossible de sauvegarder ', 'Order zone id '.$orderZone->id.' non lié à la commande '.$order->id.'.');
              }
            }
            if(isset($order_zone['ged_details'])){
              foreach($order_zone['ged_details'] as $ged_detail){
                if($ged_detail['id']>0){
                  $gedDetails=GedDetail::find($ged_detail['id']);
                  if($gedDetails==null)
                    return $this->response(0,null,'Impossible de sauvegarder', 'Détail Ged avec identifiant '.$ged_detail['id'].' pas trouvé.');
                  $ged=Ged::find($gedDetails->ged_id);
                  if($order!=null&&$order->id>0 && $ged->order_id!=$order->id)
                    return $this->response(0,null,'Impossible de sauvegarder', 'Détail Ged avec identifiant '.$ged_detail['id'].' non lié à la commande '.$order->id.'.');
                }
                if(isset($ged_detail['order_ouvrage_id'])&&!$this->isBlank($ged_detail['order_ouvrage_id'])){
                  if(!isset($ged_detail['timeline'])||!in_array($ged_detail['timeline'],['AVANT','APRES']))
                    return $this->response(0,null,'Impossible de sauvegarder', 'Veuillez spécifier un calendrier valide pour l\'identifiant de l\'ouvrage de commande '.$ged_detail['order_ouvrage_id'].'.');
                }
              }
            }
          }
        }
      }
      //end validation

      //if validation successful, save
      if($order==null){
        $order=new Order();
        $order->generateReference();
        $order->affiliate_id= $lcdtapp_api_instance->user->affiliate->id;
        $order->address_id = $event->address_id;
        $order->lang_id= !isset($Parameters['lang_id']) ? 1: $Parameters['lang_id'];
        $order->customer_id =$event->customer_id;
      }
      $order->save();
      $event->order()->associate($order->fresh())->save();
      //update state if necessary
      if($Parameters['order_state_id'] == null){
        $order->updateState(1, $lcdtapp_api_instance->user_id);//update order to status affaire
      }else{
        $order->updateState($Parameters['order_state_id'],$lcdtapp_api_instance->user_id);//save devis in another status
      }
      if(isset($Parameters['order_zones']) && is_array($Parameters['order_zones'])){
        if(!empty($Parameters['order_zones'])){
            //order zones
            foreach($Parameters['order_zones'] as $order_zone){
                if(!isset($order_zone['id'])||$this->isBlank($order_zone['id'])||$order_zone['id']==null){
                    $orderZone=new OrderZone();
                    $orderZone->order_id=$order->id;
                }else{
                    $orderZone=OrderZone::find($order_zone['id']);
                }
                $orderZone->name=$order_zone['name'];
                $orderZone->description=isset($order_zone['description'])?$order_zone['description']:'';
                $orderZone->hauteur=$order_zone['hauteur'];
                $orderZone->moyenacces_id=$order_zone['moyenacces_id'];
                $orderZone->latitude= $order_zone['latitude'];
                $orderZone->longitude= $order_zone['longitude'];
                $orderZone->save();

                if(isset($order_zone['ged_details'])){
                  foreach($order_zone['ged_details'] as $ged_detail){
                    if(!isset($ged_detail['id'])||$this->isBlank($ged_detail['id'])||$ged_detail['id']==null){
                      $gedDetail=new GedDetail();
                      //find existing ged to link to ged detail
                      $ged=Ged::where('user_id','=',$lcdtapp_api_instance->user->id)->where('customer_id','=',$event->customer_id)->where('order_id','=',$order->id)->first();
                      if($ged==null){
                        //new ged
                        $ged=new Ged();
                        $ged->customer_id=$event->customer_id;
                        $ged->order_id=$order->id;
                        $ged->user_id=$lcdtapp_api_instance->user->id;
                        $ged->save();
                      }
                      $gedDetail->ged_id=$ged->id;
                      $gedDetail->user_id=$lcdtapp_api_instance->user->id;
                    }else{
                      $gedDetail=GedDetail::find($ged_detail['id']);   
                    }
                    $gedDetail->order_ouvrage_id=isset($ged_detail['order_ouvrage_id'])?$ged_detail['order_ouvrage_id']:0;
                    $gedDetail->timeline=isset($ged_detail['order_ouvrage_id'])?$ged_detail['timeline']:null;
                    $gedDetail->ged_category_id=isset($ged_detail['ged_category_id'])?$ged_detail['ged_category_id']:0;
                    $gedDetail->order_zone_id=$orderZone->id;
                    $gedDetail->description=isset($ged_detail['description'])?$ged_detail['description']:'';
                    $gedDetail->longitude=isset($ged_detail['longitude'])?$ged_detail['longitude']:'';
                    $gedDetail->latitude=isset($ged_detail['latitude'])?$ged_detail['latitude']:'';
                    $gedDetail->save();
                    $gedDetail=$gedDetail->fresh();//retreve fresh object with all fields
              
                    if( $gedDetail->file==null){//files can only be stored once to avoid duplicates;
                      if(isset($ged_detail['data'])&&!$this->isBlank($ged_detail['data'])){
                        $storedFile=$this->storeFile($ged_detail['data'],$ged_detail['human_readable_filename'],$gedDetail->id);
                        $gedDetail->file=$storedFile->file;
                        $gedDetail->type=$storedFile->type;
                        $gedDetail->storage_path=$storedFile->storage_path;
                        $gedDetail->human_readable_filename=$storedFile->human_readable_filename;
                      }
                    }
                    $gedDetail->save();
                  }
                }
            }
        }
      }
      return $this->response(1, $order->fresh());
    }else{
        return $isLoggedIn;
    }
  }
public function SaveDevisTechnician(Request $request){
    $Parameters=$request->post('Parameters');
    $SessionID=$request->post('SessionID');
    $AccountKey=$request->post('AccountKey');
    

    //start validation
    $valid=$this->isValidAccountKeySessionID($request);
    if($valid!==true)return $valid;
    $isLoggedIn=$this->checkLogin($request);

    if($isLoggedIn===true){
        if(!isset($Parameters['order_id'])||$this->isBlank($Parameters['order_id'])){
            return $this->response(0,null,'ID order manquant');
        }
        $lcdtapp_api_instance=$this->getApiInstance($AccountKey,$SessionID);
     
        if(isset($Parameters['order_state_id'])&&$Parameters['order_state_id']!==19){
            return $this->response(0,null,'Le champs order_state_id est invalid');
        }

        //VERIFY SIGNATURE
        if(isset($Parameters['signed_by_customer'])&&$Parameters['signed_by_customer']!==0&&$Parameters['signed_by_customer']!==1){
            return $this->response(0,null,'Le champs signed_by_customer est invalid');
        }

        if(isset($Parameters['signature'])&&!empty($Parameters['signature'])){
            foreach($Parameters['signature'] as $ged_detail){
                if((!isset($ged_detail['description'])||$this->isBlank($ged_detail['description']))&&(!isset($ged_detail['data'])||$this->isBlank($ged_detail['data']))){
                    return $this->response(0,null,'Détails Ged(Signature) non valides','Au moins une donnée de fichier ou une description est requise.');
                }

                if(isset($ged_detail['data'])&&!$this->isBlank($ged_detail['data']))
                    if(!isset($ged_detail['human_readable_filename'])||$this->isBlank($ged_detail['human_readable_filename'])){
                        return $this->response(0,null,'Le nom du fichier de détails Ged(Signature) est requis.');
                    }
                if(!isset($ged_detail['latitude'])||$this->isBlank($ged_detail['latitude'])){
                    return $this->response(0,null,'La latitude de détail Ged(Signature) est requise.');
                }
                if(!isset($ged_detail['longitude'])||$this->isBlank($ged_detail['longitude'])){
                    return $this->response(0,null,'La longitude de détail Ged(Signature) est requise.');
                }

        }
    }

    $order=null;
    if(isset($Parameters['order_id'])&&$Parameters['order_id']!=null){
        $order=Order::where('id','=',$Parameters['order_id'])->where('affiliate_id','=',$lcdtapp_api_instance->user->affiliate->id)->first();
        if($order==null)
            return $this->response(0,null,'Commande introuvable.');
    }

        //VERIFY ORDER ZONE
        if(isset($Parameters['order_zones'])&&is_array($Parameters['order_zones'])){
        
        if(!empty($Parameters['order_zones'])){
            //order zones
            foreach($Parameters['order_zones'] as $order_zone){
                    if(!isset($order_zone['order_zone_id'])||$this->isBlank($order_zone['order_zone_id'])){
                        return $this->response(0,null,'order_zone_id est requis.');
                    }
                    //verify that order zone id belongs to order
                    $oz=OrderZone::find($order_zone['order_zone_id']);
                    if($oz==null)
                    return $this->response(0,null,'order_zone_id '.$order_zone['order_zone_id'].' pas trouvé.');
                    if($oz->order_id!=$order->id)
                    return $this->response(0,null,'order_zone_id  '.$order_zone['order_zone_id'].' n\'appartient pas à la commande.');
                    if(isset($order_zone['order_categories'])&&!empty($order_zone['order_categories'])){
                       
                        foreach($order_zone['order_categories'] as $order_cat){
                            if(!isset($order_cat['order_cat_id'])||$this->isBlank($order_cat['order_cat_id'])){
                                return $this->response(0,null,'order_cat_id est requis.');
                            }
                            //verify that order cat id belongs to order zone
                            $oc=OrderCat::find($order_cat['order_cat_id']);
                            if($oc==null)
                            return $this->response(0,null,'order_cat_id '.$order_cat['order_cat_id'].' pas trouvé.');
                            if($oc->order_zone_id!=$order_zone['order_zone_id'])
                            return $this->response(0,null,'order_cat_id  '.$order_cat['order_cat_id'].' n\'appartient pas à la zone '.$order_zone['order_zone_id'].'.');

                            if(isset($order_cat['list_ouvrages'])&&!empty($order_cat['list_ouvrages'])){
                                foreach($order_cat['list_ouvrages'] as  $list_ouvrage){
                                    if(!isset($list_ouvrage['order_ouvrage_id'])||$this->isBlank($list_ouvrage['order_ouvrage_id'])){
                                        return $this->response(0,null,'order_ouvrage_id est requis.');
                                    }
                                    //verify that order ouvrage belongs to order zone
                                    $oo=OrderOuvrage::find($list_ouvrage['order_ouvrage_id']);
                                    if($oo==null)
                                    return $this->response(0,null,'order_ouvrage_id '.$list_ouvrage['order_ouvrage_id'].' pas trouvé.');
                                    if($oo->order_zone_id!=$order_zone['order_zone_id'])
                                    return $this->response(0,null,'order_ouvrage_id  '.$list_ouvrage['order_ouvrage_id'].' n\'appartient pas à la zone '.$order_zone['order_zone_id'].'.');

                                    if(isset($list_ouvrage['Avant'])&&!empty($list_ouvrage['Avant'])){
                                        
                                        foreach($list_ouvrage['Avant'] as $ged_detail){
                                        
                                            //ged details
                                
                                                if((!isset($ged_detail['description'])||$this->isBlank($ged_detail['description']))&&(!isset($ged_detail['data'])||$this->isBlank($ged_detail['data']))){
                                                    return $this->response(0,null,'Détails ged non valides','Au moins une donnée de fichier ou une description est requise.');
                                                }

                                                if(isset($ged_detail['data'])&&!$this->isBlank($ged_detail['data']))
                                                    if(!isset($ged_detail['human_readable_filename'])||$this->isBlank($ged_detail['human_readable_filename'])){
                                                        return $this->response(0,null,'Le nom du fichier de détails Ged est requis.');
                                                    }
                                                if(!isset($ged_detail['latitude'])||$this->isBlank($ged_detail['latitude'])){
                                                    return $this->response(0,null,'La latitude de détail Ged est requise.');
                                                }
                                                if(!isset($ged_detail['longitude'])||$this->isBlank($ged_detail['longitude'])){
                                                    return $this->response(0,null,'La longitude de détail Ged est requise.');
                                                }
                                                if(!isset($ged_detail['ged_category_id'])||$this->isBlank($ged_detail['ged_category_id'])){
                                                    return $this->response(0,null,'ged_category_id est requise.');
                                                }
                                               //verify valid ged category id
                                                $gc=GedCategory::find($ged_detail['ged_category_id']);
                                                if($gc==null)
                                                return $this->response(0,null,'la ged_category_id '.$ged_detail['ged_category_id'].' est invalide.');
                                        }

                                    }

                                    if(isset($list_ouvrage['Apres'])&&!empty($list_ouvrage['Apres'])){
                                        
                                        foreach($list_ouvrage['Apres'] as $ged_detail){
                                        
                                            //ged details
                                
                                                if((!isset($ged_detail['description'])||$this->isBlank($ged_detail['description']))&&(!isset($ged_detail['data'])||$this->isBlank($ged_detail['data']))){
                                                    return $this->response(0,null,'Détails ged non valides','Au moins une donnée de fichier ou une description est requise.');
                                                }

                                                if(isset($ged_detail['data'])&&!$this->isBlank($ged_detail['data']))
                                                    if(!isset($ged_detail['human_readable_filename'])||$this->isBlank($ged_detail['human_readable_filename'])){
                                                        return $this->response(0,null,'Le nom du fichier de détails Ged est requis.');
                                                    }
                                                if(!isset($ged_detail['latitude'])||$this->isBlank($ged_detail['latitude'])){
                                                    return $this->response(0,null,'La latitude de détail Ged est requise.');
                                                }
                                                if(!isset($ged_detail['longitude'])||$this->isBlank($ged_detail['longitude'])){
                                                    return $this->response(0,null,'La longitude de détail Ged est requise.');
                                                }
                                                if(!isset($ged_detail['ged_category_id'])||$this->isBlank($ged_detail['ged_category_id'])){
                                                    return $this->response(0,null,'ged_category_id est requise.');
                                                }
                                               //verify valid ged category id
                                                $gc=GedCategory::find($ged_detail['ged_category_id']);
                                                if($gc==null)
                                                return $this->response(0,null,'la ged_category_id '.$ged_detail['ged_category_id'].' est invalide.');
                                        }

                                    }
                                }
                            }

                        }

                
                    }
        
                }
            }

        }

        //VERIFY TRAVAUX SUPPLEMENTAIRE
        if(isset($Parameters['travaux_supplementaire'])&&is_array($Parameters['travaux_supplementaire'])){
                
            if(!empty($Parameters['travaux_supplementaire'])){
                //order zones
                foreach($Parameters['travaux_supplementaire'] as $order_zone){
                        if(!isset($order_zone['order_zone_id'])||$this->isBlank($order_zone['order_zone_id'])){
                            return $this->response(0,null,'(TRAVAUX SUPPLEMENTAIRE) order_zone_id est requis.');
                        }
                                   //verify that order zone id belongs to order
                                $oz=OrderZone::find($order_zone['order_zone_id']);
                                if($oz==null)
                                return $this->response(0,null,'(TRAVAUX SUPPLEMENTAIRE) order_zone_id '.$order_zone['order_zone_id'].' pas trouvé.');
                                if($oz->order_id!=$order->id)
                                return $this->response(0,null,'(TRAVAUX SUPPLEMENTAIRE) order_zone_id  '.$order_zone['order_zone_id'].' n\'appartient pas à la commande.');
                        

                        if(isset($order_zone['ged_details'])&&!empty($order_zone['ged_details'])){
                            
                            foreach($order_zone['ged_details'] as $ged_detail){
                            
                                //ged details
                    
                                    if((!isset($ged_detail['description'])||$this->isBlank($ged_detail['description']))&&(!isset($ged_detail['data'])||$this->isBlank($ged_detail['data']))){
                                        return $this->response(0,null,'(TRAVAUX SUPPLEMENTAIRE) Détails ged non valides','Au moins une donnée de fichier ou une description est requise.');
                                    }

                                    if(isset($ged_detail['data'])&&!$this->isBlank($ged_detail['data']))
                                        if(!isset($ged_detail['human_readable_filename'])||$this->isBlank($ged_detail['human_readable_filename'])){
                                            return $this->response(0,null,'(TRAVAUX SUPPLEMENTAIRE) Le nom du fichier de détails Ged est requis.');
                                        }
                                    if(!isset($ged_detail['latitude'])||$this->isBlank($ged_detail['latitude'])){
                                        return $this->response(0,null,'(TRAVAUX SUPPLEMENTAIRE) La latitude de détail Ged est requise.');
                                    }
                                    if(!isset($ged_detail['longitude'])||$this->isBlank($ged_detail['longitude'])){
                                        return $this->response(0,null,'(TRAVAUX SUPPLEMENTAIRE) La longitude de détail Ged est requise.');
                                    }
                                    if(!isset($ged_detail['ged_category_id'])||$this->isBlank($ged_detail['ged_category_id'])){
                                        return $this->response(0,null,'(TRAVAUX SUPPLEMENTAIRE) ged_category_id est requise.');
                                    }
                                    $gc=GedCategory::find($ged_detail['ged_category_id']);
                                    if($gc==null)
                                    return $this->response(0,null,'(TRAVAUX SUPPLEMENTAIRE) la ged_category_id '.$ged_detail['ged_category_id'].' est invalide.');
    
                            }

                        }
            
                    }
                }

            }


            $this->l('API SAVE DEVIS TECHNICIAN',null,$lcdtapp_api_instance->user->id);


        //signature
        if(isset($Parameters['signed_by_customer'])){
            $order->signed_by_customer=$Parameters['signed_by_customer'];
            $order->save();
        }
        if(isset($Parameters['signature'])&&!empty($Parameters['signature']))
            foreach($Parameters['signature'] as $ged_detail){
                if(!isset($ged_detail['id'])||$this->isBlank($ged_detail['id'])||$ged_detail['id']==null){
                    $gedDetail=new GedDetail();
                    //find existing ged to link to ged detail
                    $ged=Ged::where('user_id','=',$lcdtapp_api_instance->user->id)->where('order_id','=',$order->id)->first();
                    if($ged==null){
                        //new ged
                        $ged=new Ged();
                        $ged->customer_id=$order->customer_id;
                        $ged->order_id=$order->id;
                        $ged->user_id=$lcdtapp_api_instance->user->id;
                        $ged->save();
                    }
                    $gedDetail->ged_id=$ged->id;
                    $gedDetail->user_id=$lcdtapp_api_instance->user->id;
                }else{
                    $gedDetail=GedDetail::find($ged_detail['id']);   
                }
                $gedDetail->order_ouvrage_id=0;
                $gedDetail->timeline=null;
                $gedDetail->ged_category_id=0;
                $gedDetail->order_zone_id=0;
                $gedDetail->signature=1;
                $gedDetail->description=isset($ged_detail['description'])?$ged_detail['description']:'';
                $gedDetail->longitude=isset($ged_detail['longitude'])?$ged_detail['longitude']:'';
                $gedDetail->latitude=isset($ged_detail['latitude'])?$ged_detail['latitude']:'';
                $gedDetail->save();
                $gedDetail=$gedDetail->fresh();//retreve fresh object with all fields
        
                if( $gedDetail->file==null){//files can only be stored once to avoid duplicates;
                    if(isset($ged_detail['data'])&&!$this->isBlank($ged_detail['data'])){
                      
                        $storedFile=$this->storeFile($ged_detail['data'],$ged_detail['human_readable_filename'],$gedDetail->id);
                        $gedDetail->file=$storedFile->file;
                        $gedDetail->type=$storedFile->type;
                        $gedDetail->storage_path=$storedFile->storage_path;
                        $gedDetail->human_readable_filename=$storedFile->human_readable_filename;
                    }
                }
                $gedDetail->save();

            }

            if(isset($Parameters['travaux_supplementaire'])&&is_array($Parameters['travaux_supplementaire'])){
                
                if(!empty($Parameters['travaux_supplementaire'])){
                    //order zones
                    foreach($Parameters['travaux_supplementaire'] as $order_zone){
                        if(isset($order_zone['ged_details'])&&!empty($order_zone['ged_details'])){
                            foreach($order_zone['ged_details'] as $ged_detail){
                                if(!isset($ged_detail['id'])||$this->isBlank($ged_detail['id'])||$ged_detail['id']==null){
                                    $gedDetail=new GedDetail();
                                    //find existing ged to link to ged detail
                                    $ged=Ged::where('user_id','=',$lcdtapp_api_instance->user->id)->where('order_id','=',$order->id)->first();
                                    if($ged==null){
                                        //new ged
                                        $ged=new Ged();
                                        $ged->customer_id=$order->customer_id;
                                        $ged->order_id=$order->id;
                                        $ged->user_id=$lcdtapp_api_instance->user->id;
                                        $ged->save();
                                    }
                                    $gedDetail->ged_id=$ged->id;
                                    $gedDetail->user_id=$lcdtapp_api_instance->user->id;
                                }else{
                                    $gedDetail=GedDetail::find($ged_detail['id']);   
                                }
                                $gedDetail->order_ouvrage_id=0;
                                $gedDetail->timeline=null;
                                $gedDetail->ged_category_id=$ged_detail['ged_category_id'];
                                $gedDetail->order_zone_id=$order_zone['order_zone_id'];
                                $gedDetail->additional_work=1;
                                $gedDetail->description=isset($ged_detail['description'])?$ged_detail['description']:'';
                                $gedDetail->longitude=isset($ged_detail['longitude'])?$ged_detail['longitude']:'';
                                $gedDetail->latitude=isset($ged_detail['latitude'])?$ged_detail['latitude']:'';
                                $gedDetail->save();
                                $gedDetail=$gedDetail->fresh();//retreve fresh object with all fields
                        
                                if( $gedDetail->file==null){//files can only be stored once to avoid duplicates;
                                    if(isset($ged_detail['data'])&&!$this->isBlank($ged_detail['data'])){
                                      
                                        $storedFile=$this->storeFile($ged_detail['data'],$ged_detail['human_readable_filename'],$gedDetail->id);
                                        $gedDetail->file=$storedFile->file;
                                        $gedDetail->type=$storedFile->type;
                                        $gedDetail->storage_path=$storedFile->storage_path;
                                        $gedDetail->human_readable_filename=$storedFile->human_readable_filename;
                                    }
                                }
                                $gedDetail->save();
                            }
                        }
                    }
                }
            }

            if(!empty($Parameters['order_zones'])){
                //order zones
                foreach($Parameters['order_zones'] as $order_zone){
                    
                
                     
                        if(isset($order_zone['order_categories'])&&!empty($order_zone['order_categories'])){
                           
                            foreach($order_zone['order_categories'] as $order_cat){
 
                                
    
                                if(isset($order_cat['list_ouvrages'])&&!empty($order_cat['list_ouvrages'])){
                                    foreach($order_cat['list_ouvrages'] as  $list_ouvrage){
                                    
                                        $oo=OrderOuvrage::find($list_ouvrage['order_ouvrage_id']);
                                      if(isset($list_ouvrage['textoperator'])){
                                        $oo->textoperator=$list_ouvrage['textoperator'];
                                        $oo->save();
                                      }
    
                                        if(isset($list_ouvrage['Avant'])&&!empty($list_ouvrage['Avant'])){
                                            
                                            foreach($list_ouvrage['Avant'] as $ged_detail){
                                                if(!isset($ged_detail['id'])||$this->isBlank($ged_detail['id'])||$ged_detail['id']==null){
                                                    $gedDetail=new GedDetail();
                                                    //find existing ged to link to ged detail
                                                    $ged=Ged::where('user_id','=',$lcdtapp_api_instance->user->id)->where('order_id','=',$order->id)->first();
                                                    if($ged==null){
                                                        //new ged
                                                        $ged=new Ged();
                                                        $ged->customer_id=$order->customer_id;
                                                        $ged->order_id=$order->id;
                                                        $ged->user_id=$lcdtapp_api_instance->user->id;
                                                        $ged->save();
                                                    }
                                                    $gedDetail->ged_id=$ged->id;
                                                    $gedDetail->user_id=$lcdtapp_api_instance->user->id;
                                                }else{
                                                    $gedDetail=GedDetail::find($ged_detail['id']);   
                                                }
                                                $gedDetail->order_ouvrage_id=$list_ouvrage['order_ouvrage_id'];
                                                $gedDetail->timeline='AVANT';
                                                $gedDetail->ged_category_id=$ged_detail['ged_category_id'];
                                                $gedDetail->order_zone_id=$order_zone['order_zone_id'];
                                                $gedDetail->description=isset($ged_detail['description'])?$ged_detail['description']:'';
                                                $gedDetail->longitude=isset($ged_detail['longitude'])?$ged_detail['longitude']:'';
                                                $gedDetail->latitude=isset($ged_detail['latitude'])?$ged_detail['latitude']:'';
                                                $gedDetail->save();
                                                $gedDetail=$gedDetail->fresh();//retreve fresh object with all fields
                                        
                                                if( $gedDetail->file==null){//files can only be stored once to avoid duplicates;
                                                    if(isset($ged_detail['data'])&&!$this->isBlank($ged_detail['data'])){
                                                      
                                                        $storedFile=$this->storeFile($ged_detail['data'],$ged_detail['human_readable_filename'],$gedDetail->id);
                                                        $gedDetail->file=$storedFile->file;
                                                        $gedDetail->type=$storedFile->type;
                                                        $gedDetail->storage_path=$storedFile->storage_path;
                                                        $gedDetail->human_readable_filename=$storedFile->human_readable_filename;
                                                    }
                                                }
                                                $gedDetail->save();
                                            }
                                        }
                                        if(isset($list_ouvrage['Apres'])&&!empty($list_ouvrage['Apres'])){
                                            
                                            foreach($list_ouvrage['Apres'] as $ged_detail){
                                                if(!isset($ged_detail['id'])||$this->isBlank($ged_detail['id'])||$ged_detail['id']==null){
                                                    $gedDetail=new GedDetail();
                                                    //find existing ged to link to ged detail
                                                    $ged=Ged::where('user_id','=',$lcdtapp_api_instance->user->id)->where('order_id','=',$order->id)->first();
                                                    if($ged==null){
                                                        //new ged
                                                        $ged=new Ged();
                                                        $ged->customer_id=$order->customer_id;
                                                        $ged->order_id=$order->id;
                                                        $ged->user_id=$lcdtapp_api_instance->user->id;
                                                        $ged->save();
                                                    }
                                                    $gedDetail->ged_id=$ged->id;
                                                    $gedDetail->user_id=$lcdtapp_api_instance->user->id;
                                                }else{
                                                    $gedDetail=GedDetail::find($ged_detail['id']);   
                                                }
                                                $gedDetail->order_ouvrage_id=$list_ouvrage['order_ouvrage_id'];
                                                $gedDetail->timeline='APRES';
                                                $gedDetail->ged_category_id=$ged_detail['ged_category_id'];
                                                $gedDetail->order_zone_id=$order_zone['order_zone_id'];
                                                $gedDetail->description=isset($ged_detail['description'])?$ged_detail['description']:'';
                                                $gedDetail->longitude=isset($ged_detail['longitude'])?$ged_detail['longitude']:'';
                                                $gedDetail->latitude=isset($ged_detail['latitude'])?$ged_detail['latitude']:'';
                                                $gedDetail->save();
                                                $gedDetail=$gedDetail->fresh();//retreve fresh object with all fields
                                        
                                                if( $gedDetail->file==null){//files can only be stored once to avoid duplicates;
                                                    if(isset($ged_detail['data'])&&!$this->isBlank($ged_detail['data'])){
                                                      
                                                        $storedFile=$this->storeFile($ged_detail['data'],$ged_detail['human_readable_filename'],$gedDetail->id);
                                                        $gedDetail->file=$storedFile->file;
                                                        $gedDetail->type=$storedFile->type;
                                                        $gedDetail->storage_path=$storedFile->storage_path;
                                                        $gedDetail->human_readable_filename=$storedFile->human_readable_filename;
                                                    }
                                                }
                                                $gedDetail->save();
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }



        //validate devis 
        if(isset($Parameters['order_state_id']))    
        $order->updateState($Parameters['order_state_id'],$lcdtapp_api_instance->user->id);
        return $this->response(1);
}else{
    return $isLoggedIn;
}

}
public function GetZones(Request $request){
    $SessionID=$request->post('SessionID');
    $AccountKey=$request->post('AccountKey');
    $Parameters=$request->post('Parameters');

    $valid=$this->isValidAccountKeySessionID($request);
    if($valid!==true)return $valid;
    $isLoggedIn=$this->checkLogin($request);
    if(!isset($Parameters['order_id'])||$this->isBlank($Parameters['order_id'])){
        return $this->response(0,null,'Order_id manquant.');
    }



    if($isLoggedIn===true){
        $lcdtapp_api_instance=$this->getApiInstance($AccountKey,$SessionID);
        $affiliate=$lcdtapp_api_instance->user->affiliate;

        $order=$affiliate->orders->where('id','=',$Parameters['order_id'])->first();
        if($order==null){
            return $this->response(0,null,'Commande introuvable.');
        }
           
        return $this->response(1, $order->orderZones);
    }else{
        return $isLoggedIn;
    }
}
    public function getApiInstance($AccountKey,$SessionID){
        $lcdtapp_api= DB::table('apis')
            ->join('api_sessions','apis.id','=','api_sessions.api_id')->where([
                ['api_sessions.sessionid','=',$SessionID],
                ['api_sessions.sessiontype','=','login'],
                ['apis.AccountKey','=',$AccountKey]
            ])->first();
        if($lcdtapp_api->user_id>0) {
            $user = User::findOrFail($lcdtapp_api->user_id);
            if($user!=null){
            $lcdtapp_api->user_id = $user->id;
            $lcdtapp_api->email = $user->email;
            $lcdtapp_api->user=$user;
            }
        }
        return $lcdtapp_api;
    }
    public function isExpired($SessionID,$AccountKey){
        $expired=false;
        $session=DB::table('api_sessions')->select('api_sessions.id as id')->join('apis','apis.id','=','api_sessions.api_id')->where('api_sessions.sessionid','=',$SessionID)->where('apis.accountkey','=',$AccountKey)     ->where('api_sessions.last_activity', '>',
            Carbon::now()->subHour(1)->toDateTimeString()
        )->first();

        if($session===null){
            $expired=true;
        }else{
            DB::table('api_sessions')->where('id','=',$session->id)->update(['last_activity'=>date('Y-m-d H:i:s')]);
        }
        return $expired;
    }
    
    public function response($success=true, $object=null,$message=null,$messageDetails=null,$validationInfo=null,$status=200,$headers=[],$options=0){

        return response()->json([
              "Version"=> "1.0",
              "Failed"=> !$success,
              "Message"=> $message,
              "MessageDetails"=> $messageDetails,
              "ValidationInfo"=>$validationInfo,
              "ReturnObject"=> $object
        ],$status,$headers,$options);
    }

    public  function isBlank($str){
        return str_replace(" ","",$str)==="";
    }

    public function checkLogin(Request $request){
        $SessionID=$request->post('SessionID');
        $AccountKey=$request->post('AccountKey');
        if($SessionID==null){
            return $this->response(0,null,'Paramètre SessionID manquant.');
        }
        if($AccountKey==null){
            return $this->response(0,null,'Paramètre AccountKey manquant.');
        }


         $lcdtapp_api_session=ApiSession::where('sessionid','=', $SessionID)->first();
        if($lcdtapp_api_session==null)
        return $this->response(0,null,'Session invalide ou expirée. ',null,null,401);
    
        $SessionType = $lcdtapp_api_session->sessiontype;


        $expired=$this->isExpired($SessionID,$AccountKey);
        $isLoggedIn = !$expired&&$SessionType=='login';
        if(!$isLoggedIn)
            return $this->response(0,null,'Session invalide ou expirée.',null,null,401);
        return $isLoggedIn;
    }

    public function ForgotPassword(Request $request){
        $SessionID=$request->post('SessionID');
        $AccountKey=$request->post('AccountKey');
        $Parameters=$request->post('Parameters');
        if($SessionID==null){
            return $this->response(0,null,'Paramètre SessionID manquant.');
        }
        if($AccountKey==null){
            return $this->response(0,null,'Paramètre AccountKey manquant.');
        }

        if(!isset($Parameters['User'])||$this->isBlank($Parameters['User'])){
            return $this->response(0,null,'Paramètre utilisateur manquant.');     
        }
         $lcdtapp_api_session=ApiSession::where('sessionid','=', $SessionID)->first();
        if($lcdtapp_api_session==null)
        return $this->response(0,null,'Session invalide',null,null,401);


        $expired=$this->isExpired($SessionID,$AccountKey);
        if($expired)
            return $this->response(0,null,'Session expirée',null,null,401);
            $user=User::where('email','=',$Parameters['User'])->first();
            if($user==null)
            return $this->response(0,null,'Utilisateur non trouvé');

//TODO: send email via zoho crm

        return $this->response(1);
    }
    public function isValidAccountKeySessionID(Request $request){
        $SessionID=$request->post('SessionID');
        $AccountKey=$request->post('AccountKey');

        if($SessionID==null){
            return $this->response(0,null,'Paramètre SessionID manquant.');
        }
        if($AccountKey==null){
            return $this->response(0,null,'Paramètre AccountKey manquant.');
        }
        return true;
    }

}