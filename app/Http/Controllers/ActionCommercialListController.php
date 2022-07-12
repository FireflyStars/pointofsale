<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Event;
use App\Models\EventStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\TableFiltersController;
use App\Http\Resources\ActionCommercialListResource;
use App\Models\EventHistory;
use Illuminate\Support\Facades\Auth;

use League\OAuth2\Client\Provider\GenericProvider;
use App\TokenStore\TokenCache;
use Microsoft\Graph\Graph;
use Microsoft\Graph\Model;

class ActionCommercialListController extends Controller
{

    public function syncOutlook()
    {
      // Initialize the OAuth client
      $oauthClient = new GenericProvider([
        'clientId'                => config('azure.appId'),
        'clientSecret'            => config('azure.appSecret'),
        'redirectUri'             => config('azure.redirectUri'),
        'urlAuthorize'            => config('azure.authority').config('azure.authorizeEndpoint'),
        'urlAccessToken'          => config('azure.authority').config('azure.tokenEndpoint'),
        'urlResourceOwnerDetails' => '',
        'scopes'                  => config('azure.scopes')
      ]);
  
      $authUrl = $oauthClient->getAuthorizationUrl();
  
      // Save client state so we can validate in callback
      session(['oauthState' => $oauthClient->getState()]);
  
      // Redirect to AAD signin page
      return redirect()->away($authUrl);
    }

    public function outlookSyncCallback(Request $request)
    {
      // Validate state
      $expectedState = session('oauthState');
      $request->session()->forget('oauthState');
      $providedState = $request->query('state');
  
      if (!isset($expectedState)) {
        // If there is no expected state in the session,
        // do nothing and redirect to the home page.
        return redirect('/');
      }
  
      if (!isset($providedState) || $expectedState != $providedState) {
        return redirect('/')
          ->with('error', 'Invalid auth state')
          ->with('errorDetail', 'The provided auth state did not match the expected value');
      }
  
      // Authorization code should be in the "code" query param
      $authCode = $request->query('code');
      if (isset($authCode)) {
        // Initialize the OAuth client
        $oauthClient = new \League\OAuth2\Client\Provider\GenericProvider([
          'clientId'                => config('azure.appId'),
          'clientSecret'            => config('azure.appSecret'),
          'redirectUri'             => config('azure.redirectUri'),
          'urlAuthorize'            => config('azure.authority').config('azure.authorizeEndpoint'),
          'urlAccessToken'          => config('azure.authority').config('azure.tokenEndpoint'),
          'urlResourceOwnerDetails' => '',
          'scopes'                  => config('azure.scopes')
        ]);
  
        // <StoreTokensSnippet>
        try {
          // Make the token request
          $accessToken = $oauthClient->getAccessToken('authorization_code', [
            'code' => $authCode
          ]);
  
          $graph = new Graph();
          $graph->setAccessToken($accessToken->getToken());
  
          $user = $graph->createRequest('GET', '/me?$select=displayName,mail,mailboxSettings,userPrincipalName')
            ->setReturnType(Model\User::class)
            ->execute();
  
          $tokenCache = new TokenCache();
          $tokenCache->storeTokens($accessToken, $user);
  
          return redirect('/');
        }
        // </StoreTokensSnippet>
        catch (\League\OAuth2\Client\Provider\Exception\IdentityProviderException $e) {
          return redirect('/')
            ->with('error', 'Error requesting access token')
            ->with('errorDetail', json_encode($e->getResponseBody()));
        }
      }
  
      return redirect('/')
        ->with('error', $request->query('error'))
        ->with('errorDetail', $request->query('error_description'));
    }

    public function index(Request $request) 
    {

        $data = $this->get_data($request);

        $data = $data->where('events.affiliate_id', $request->user()->affiliate_id)
        ->take($request->take ?? 15)
        ->skip($request->skip ?? 0)
        ->get();            

        return response()->json($data);  

    }


    public function list_user(Request $request) 
    {

        $data = $this->get_data($request);

        $data = $data->where('events.user_id', $request->user()->id)
        ->take($request->take ?? 15)
        ->skip($request->skip ?? 0)
        ->get();            

        return response()->json($data); 

    }

    public function change_event_status(Event $event, Request $request) 
    {

        $event->event_status_id = $request->statusId;

        if($request->annuler == false) 
        {
            $event->deleted_at = now();
        }

        $event->save();

        $event->eventHistory()->create([
            'event_statut_id' => 17,
            'comment'         => "Effacer event",
            'user_id'         => $request->user()->id,
        ]);

        return response()->json("Event status changed");

    }

    public function get_event_history(Event $event, Request $request) 
    {
        return response()->json(
            $event->eventHistory()
            ->latest('created_at')
            ->when($request->has('limit') && $request->limit > 0, function($query) {
                $query->limit('3');
            })
            ->get()
            ->load('user', 'status')
        );
    }

    public function get_details($id) 
    {
        DB::enableQueryLog();
        return response()->json(
            new ActionCommercialListResource(
                Event::find($id)
            )
        );

    }

    public function get_event_users(Event $event) 
    {
        return response()->json(
            User::where('affiliate_id', $event->affiliate_id)->get()
        );
    }

    public function get_event_statuses() 
    {
        return response()->json(
            EventStatus::all()
        );   
    }

    public function change_event_user(Event $event, Request $request) 
    {
        $event->user_id = $request->userId;
        $event->save();
        return response()->json($event->user);
    }

    public function change_event_date(Request $request, Event $event) 
    {

        $datedebut = Carbon::parse($event->datedebut)->format('Y-m-d');
        $datefin = Carbon::parse($event->datefin)->format('Y-m-d');
        
        $event->update([
            'datedebut' => $request->datedebut . ' ' . $request->datedebutTime . ':00',
            'datefin'   => $datefin . ' ' . $request->datefinTime . ':00'
        ]);

        $status = EventStatus::where('name', 'Replanification')->first();

        $newDateDebut = Carbon::parse($event->datedebut)->format('Y-m-d');

        $event->eventHistory()->create([
            'event_statut_id' => $status->id ?? 17,
            'comment'         => "$datedebut (Changed date: $newDateDebut, start hours: $request->datedebutTime)",
            'user_id'         => $request->user()->id,
        ]);

        return response()->json("Event dates updated");

    }


    public function statuses_formatted() 
    {
        return response()->json(
            EventStatus::select('id', 'name as value')->get()
        );
    }

    public function statuses() 
    {
        return response()->json(
            EventStatus::all()
        );
    }


    private function get_data(Request $request) 
    {

        $data = Event::query()
        ->leftJoin('contacts', 'contacts.id', '=', 'events.contact_id')
        ->leftJoin('addresses', 'addresses.id', '=', 'events.address_id')
        ->leftJoin('users', 'users.id', '=', 'events.user_id')
        ->leftJoin('event_types', 'event_types.id', '=', 'events.event_type_id')
        ->leftJoin('event_statuses', 'event_statuses.id', '=', 'events.event_status_id')
        ->leftJoin('event_origins', 'event_origins.id', '=', 'events.event_origin_id')
        ->select(
            'events.id',
            'users.name as client_name',
            'events.name as action',
            'event_types.name as event_type',
            'event_statuses.id as event_status_id',
            'event_origins.name as origin',
            DB::raw('DATE_FORMAT(events.datedebut, "%Y-%m-%d") as action_date'),
            DB::raw(
                'TRIM(
                    CONCAT(
                        contacts.name,
                        "<br>",
                        contacts.email,
                        "<br>",
                        contacts.mobile
                    )
                ) 
            as contact'), 
            DB::raw(
                'TRIM(
                    CONCAT(
                        addresses.firstname, " ", addresses.lastname,
                        addresses.address1,
                        "<br>",
                        IFNULL(addresses.address2, ""),
                        IF(addresses.address2, "<br>", ""),
                        addresses.postcode,
                        "<br>", 
                        addresses.city
                    )
                ) 
            as address'),
            DB::raw('DATE_FORMAT(events.created_at, "%Y-%m-%d") as created_at'),
        );

        $data = (new TableFiltersController)->sorts($request, $data, 'events.id');
        $data = (new TableFiltersController)->filters($request, $data);


        return $data;

    }

    /**
     * get necessary infos to create action commercial
     */

    public function getActionInfo(){
        return response()->json([
            'actionStatus'  => DB::table('event_statuses')->select('id as value', 'name as display')->orderBy('id')->get(),
            'actionCos'     => DB::table('event_actioncos')->select('id as value', 'name as display')->orderBy('id')->get(),
            'actionType'    => DB::table('event_types')->select('id as value', 'name as display')->orderBy('id')->get(),
            'actionOrigin'  => DB::table('event_origins')->select('id as value', 'name as display')->orderBy('id')->get(),
            'users'         => DB::table('users')->select('id as value', 'name as display')->orderBy('id')->get(),
            'userId'        => Auth::id(),
        ]);
    }

    /**
     * Create a action
     */
    public function createAction(Request $request){
        $event = new Event();
        $event->contact_id = $request->contact['id'];
        $event->event_origin_id   = $request->originId;
        $event->affiliate_id      = Auth::user()->affiliate_id;
        $event->event_actionco_id = $request->actioncosId;
        $event->event_type_id     = $request->typeId;
        $event->event_status_id   = $request->statusId;
        $event->customer_id       = $request->customer['id'] ?? 0;
        $event->address_id        = $request->address['id'] ?? 0;
        $event->user_id           = $request->userId;
        $event->name              = $request->name;
        $event->description       = $request->description;
        $event->datedebut         = Carbon::parse($request->date)->format('Y-m-d').' '.$request->startTime['hours'].':'.$request->startTime['minutes'].':00';
        $event->datefin           = Carbon::parse($request->date)->format('Y-m-d').' '.$request->endTime['hours'].':'.$request->endTime['minutes'].':00';
        $event->save();

        $eventHistory = new EventHistory();
        $eventHistory->event_id = $event->id;
        $eventHistory->event_statut_id = $event->event_status_id; // otherwise set it 1
        $eventHistory->comment = "Nouveau event";
        $eventHistory->user_id = $event->user_id;
        $eventHistory->save();

        return response()->json(['success'=>true, 'id'=>$event->id]);
    }

    /**
     * update a action
     */

    public function updateAction(Request $request, Event $event){
        $event->contact_id = $request->contact['id'];
        $event->event_origin_id   = $request->originId;
        $event->affiliate_id      = Auth::user()->affiliate_id;
        $event->event_actionco_id = $request->actioncosId;
        $event->event_type_id     = $request->typeId;
        $event->event_status_id   = $request->statusId;
        $event->customer_id       = $request->customer['id'] ?? 0;
        $event->address_id        = $request->address['id'] ?? 0;
        $event->user_id           = $request->userId;
        $event->name              = $request->name;
        $event->description       = $request->description;
        $event->datedebut         = Carbon::parse($request->date)->format('Y-m-d').' '.$request->startTime['hours'].':'.$request->startTime['minutes'].':00';
        $event->datefin           = Carbon::parse($request->date)->format('Y-m-d').' '.$request->endTime['hours'].':'.$request->endTime['minutes'].':00';
        $event->save();

        $event->eventHistory()->create([
            'event_statut_id' => $event->event_status_id,
            'comment'         => "(Changed date: ".Carbon::parse($request->date)->format('Y-m-d').", start hour: ".$request->startTime['hours'].':'.$request->startTime['minutes'].':00'.")",
            'user_id'         => Auth::id(),
        ]);        

        return response()->json(true);
    }

    /**
     * Get a action
     */
    public function getAction(Event $event){

        return response()->json([
            'action'    => [ 
                'id'        => $event->id,
                'name'      => $event->name,
                'actioncosId'=> $event->event_actionco_id,
                'typeId'    => $event->event_type_id,
                'originId'  => $event->event_origin_id,
                'statusId'  => $event->event_status_id,
                'statusId'  => $event->event_status_id,
                'date'      => Carbon::parse($event->datedebut)->format('m/d/Y'),
                'startTime' => ['hours'=> Carbon::parse($event->datedebut)->format('H'), 'minutes'=> Carbon::parse($event->datedebut)->format('i')],
                'endTime'   => ['hours'=> Carbon::parse($event->datefin)->format('H'), 'minutes'=> Carbon::parse($event->datefin)->format('i')],
                'userId'    => $event->user_id,
                'description'=> $event->description,
                'created_at'=> Carbon::parse($event->created_at)->format('m/d/Y'),
                'updated_at'=> Carbon::parse($event->updated_at)->format('m/d/Y'),
            ],
            'customer'  => 
                DB::table('customers')
                ->join('group', 'group.id', '=', 'customers.group_id')
                ->join('taxes', 'taxes.id', '=', 'customers.taxe_id')
                ->where('customers.id', $event->customer_id)
                ->select('customers.id', 'customers.company', 'customers.raisonsociale', 'group.name as group',
                    DB::raw('CONCAT(customers.firstname, " ", customers.name) as contact'), 'telephone', 'taxes.name as tax',
                    'customers.naf', 'customers.siret'
                )->first(),
            'contact'  => 
                DB::table('contacts')
                ->join('contact_qualite', 'contact_qualite.id', '=', 'contacts.contact_qualite_id')
                ->where('contacts.id', $event->contact_id)
                ->select('contacts.id', 'contacts.firstname', 'contacts.name as nom',
                    DB::raw('CONCAT(contacts.firstname, " ", contacts.name) as name'),
                    'contacts.email', 'contacts.mobile', 'contact_qualite.name as qualite'
                )->first(),
            'address'  => 
                DB::table('addresses')
                ->where('id', $event->address_id)
                ->select('id', 'firstname', 'lastname as nom',
                    DB::raw('CONCAT(firstname, " ", lastname) as name'),
                    'address1', 'address2', 'address3', 'postcode', 'city'
                )->first(),
            'actionStatus'  => DB::table('event_statuses')->select('id as value', 'name as display')->orderBy('id')->get(),
            'actionCos'     => DB::table('event_actioncos')->select('id as value', 'name as display')->orderBy('id')->get(),
            'actionType'    => DB::table('event_types')->select('id as value', 'name as display')->orderBy('id')->get(),
            'actionOrigin'  => DB::table('event_origins')->select('id as value', 'name as display')->orderBy('id')->get(),
            'users'         => DB::table('users')->select('id as value', 'name as display')->orderBy('id')->get(),            
        ]);
    }
}   

