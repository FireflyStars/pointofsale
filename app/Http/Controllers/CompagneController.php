<?php

namespace App\Http\Controllers;
use Mail;
use Carbon\Carbon;
use App\Mail\MyDemoMail;
use App\Models\Campagne;
use App\Traits\Sarbacane;
use App\Models\page_builder;
use Illuminate\Http\Request;
use App\Models\campagne_card;
use App\Models\CompagneCible;
use App\Models\CampagneCategory;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use App\Models\campagne_card_detail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use App\Notifications\CourierNotification;
use Illuminate\Support\Facades\Notification;
use App\Notifications\campagneCardNotification;

class CompagneController extends Controller
{

    use Sarbacane;

    private $pdf;

    public function get_card_quantity(Request $request) 
    {
        
        $card = campagne_card::where('user_id', $request->user()->id)->where('status', 'NOUVEAU')->first();

        return response()->json($card->details->sum('qty'));

    }

    public function generate_pdf(CampagneCategory $campagne, Request $request) 
    {

        $pdf = App::make('dompdf.wrapper');

        $pdf->setOptions([
            'enable_php'           => true,
            'isRemoteEnabled'      => true, 
            'isHtml5ParserEnabled' => true, 
        ]);

        $fields = $this->fields_for_marketing($campagne, $request);
    
        $data = array(
            'fields'           => $fields,
            'image2'           => $campagne->imagetemplate,
            'image'            => $campagne->urlimageflyerpage1,
            'campagneCategory' => $campagne
        );

        $pdf->loadView(
            'product', [
                'builder' => (new page_builder),
                'data'    => $data
            ]
        );

        return $pdf->download('Product.pdf');

    }

    public function campagne_details(Campagne $campagne) 
    {
        return response()->json(
            $campagne->load('user', 'card', 'details')
        );
    }


    public function valider_card(Request $request) 
    {

        $user = $request->user();
        $affiliate = $user->affiliate;
        
        $card = optional(campagne_card::where('user_id', $user->id))->first();

        $campagne = $this->create_card_campagne($card, $user, $affiliate);

        $cardProducts = $this->get_card_campagne_products($card);

        [$transport, $montant] = $this->get_products_transport_montant($cardProducts);

        $campagne->details()->createMany($cardProducts->toArray());

        campagne_card_detail::where('campagne_card_id', $card->id)->delete();

        $card->update([
            'status' => 'VALIDER', 
        ]);

        $campagne->update([
            'transport' => $transport, 
            'montant' => $montant
        ]);

        $card->delete();

        $this->notify_admin($campagne, $user);

        return response()->json("Campagne valider completed");

    }

    private function get_products_transport_montant($products) 
    {
        
        $products = new Collection($products);

        $product_price = function($product) {
            return $product->price * $product->qty;
        };

        $product_poids = function($product) {
            return $product->qty * $product->campagneCategory->poids;
        };

        $totalTax = $products->map(function($product) {
            return $product->tax->taux ?? 0.20;
        })->sum();

        $transport = $products->sum(function($product) use($product_poids) {
            $poids = $product_poids($product);
            $weight = $poids - 10;
            $weight = (int) ceil($weight / 10);
            $transportPrice = 17 + ($weight * 7);
            return ($transportPrice) * 1;
        });

        $cardPrice = $products->sum(function($product) use($product_price) {
            return $product_price($product);
        });

        $totalHt = $cardPrice + $transport;

        $totalTtc = $totalHt + ( $totalHt * $totalTax );

        return [
            $transport,
            $totalTtc
        ];


    }

    private function notify_admin($campagne, $user) 
    {
        $emails = $this->get_settings_email();
        $emails = explode(',', $emails);

        foreach($emails as $mail) 
        {
            Notification::route('mail', $mail)
            ->notify(new campagneCardNotification($campagne));
        }

        $user->notify(new campagneCardNotification($campagne));

    }

    private function get_card_campagne_products($card) 
    {
        return campagne_card_detail::where('campagne_card_id', $card->id)
                        ->get()
                        ->except([
                            'campagne_card_id',
                        ]);
    }

    private function create_card_campagne($card, $user, $affiliate) 
    {
        return $card->campagne()->create([
            'datefinvalidatiom' => date("Y-m-d"),
            'datelancement'     => date("Y-m-d"),
            'user_id'           => $user->id,
            'affiliate_id'      => $affiliate->id,
            'type'              => 'COMMANDE PRODUIT',
            'run'               =>  date("Y-m-d"),
            'montant'           => 0,
            'nb'                => 0,
            'name'              => 'PANIER XXXX',
            'status'            => 'CAMPAGNE ENVOYEE',
        ]);
    }

    public function delete_card_product(campagne_card_detail $card) 
    {
        $card->delete();
        return response()->json("Card udpated");
    }

    public function update_card_product(Request $request, campagne_card_detail $card) 
    {
        $card->update([
            'qty' => $request->qty
        ]);

        return response()->json("Card updated");
    }


    public function get_card_products(Request $request) 
    {
        
        $affiliate = $request->user()->affiliate;
        $card = optional(campagne_card::where('user_id', $request->user()->id)->where('status', 'NOUVEAU'))->first();
        
        $card_details = campagne_card_detail::with('campagneCategory', 'tax')
                        ->where('campagne_card_id', optional($card)->id)
                        ->get();

        return response()->json(
            compact('affiliate', 'card_details')
        );

    }

    public function store_campagne_product(CampagneCategory $campagne, Request $request) 
    {
        
        $user = $request->user();

        if(!is_null($campagne->cardDetail)) 
        {
            $campagne->cardDetail()->update([
                'qty' => $campagne->cardDetail->qty + $request->qty
            ]);
        }
        else 
        {

            $card = campagne_card::where('user_id', $user->id)->where('status', 'NOUVEAU')->first();

            if(is_null($card)) 
            {
                $card = $user->card()->create([
                    'affiliate_id' => $user->affiliate->id,
                    'montant'      => 0,
                    'transport'    => 0,
                    'name'         => $campagne->name,  
                ]);
            }

            $detail = $campagne->cardDetail()->create([
                'campagne_card_id' => $card->id,      
                'productname'      => $campagne->name,
                'description'      => $campagne->text,
                'name'             => $campagne->name,
                'qty'              => $request->qty,
                'fields'           => $campagne->fields,
                'price'            => $campagne->price,
                'tax_id'           => 3,
                'email'            => $request->email,
                'phone'            => $request->phone,   
            ]);

            if(!is_null($campagne->fields)) 
            {
                $file_path = '/' . 'mail-files/' . $campagne->id . '_product_' . date('dmY-hi') . '.pdf';
                $fields = $this->fields_for_marketing($campagne, $request, $request->email, $request->phone);
    
                $data = array(
                    'fields'           => $fields,
                    'image2'           => $campagne->imagetemplate,
                    'image'            => $campagne->urlimageflyerpage1,
                    'campagneCategory' => $campagne
                );
    
                $this->save_campagne_pdf_file($file_path, $data);
                $detail->update([ 'urlpdf' => $file_path ]);
            }

        }


        $affiliate = $user->affiliate->load('tax');
        $campagne = $campagne->load('cardDetail');

        return response()->json(
            ['data' => compact('affiliate', 'campagne')]
        );
    
    }

    public function get_campagne_category(CampagneCategory $campagne, Request $request) 
    {

        $affiliate = $request->user()->affiliate;
        $campagne = $campagne->load('cardDetail');
        
        // [$width, $height] = getimagesize($campagne->imageTemplateUrl);

        $width = 0;
        $height = 0;

        return response()->json(
            ['data' => compact('affiliate', 'campagne', 'width', 'height')]
        );
    }

    public function save_letter_pdf(Request $request, Campagne $campagne) 
    {
       
        $pdf = App::make('dompdf.wrapper');

        $pdf->setOptions([
            'enable_php'           => true,
            'isRemoteEnabled'      => true, 
            'isHtml5ParserEnabled' => true, 
        ]);

        $pdf->loadView(
            'letter', [
                'campagne'  => $campagne,
                'builder'   => (new page_builder),
                'affiliate' => $request->user()->affiliate,
                'data'      => (new CompagneController)->lettredata_pdf($campagne->id),
            ]
        );

        $file_path = '/' . 'mail-files/' . $campagne->id . '_letter.pdf';

        $pdf->save(Storage::path('public') . $file_path);

        $campagne->update([
            'urllettre' => $file_path
        ]);

        return response()->json($file_path, 200);

    }

    public function save_letter_settings(Request $request, Campagne $campagne) 
    {

        $data = [
            'email' => $request->input_email,
            'phone' => $request->input_coord
        ];
        
        if($request->has('input_content')) 
        {
            $data['lettreaccompagnement'] = $request->input_content;
        }

        $campagne->update($data); 
        
        return response()->json(200);

    }

    public function stream_letter_pdf(Request $request, Campagne $campagne) 
    {
        
        $pdf = App::make('dompdf.wrapper');

        $pdf->setOptions([
            'enable_php'           => true,
            'isRemoteEnabled'      => true, 
            'isHtml5ParserEnabled' => true, 
        ]);

        $pdf->loadView(
            'letter', [
                'campagne'  => $campagne,
                'builder'   => (new page_builder),
                'affiliate' => $request->user()->affiliate,
                'data'      => (new CompagneController)->lettredata_pdf($campagne->id),
            ]
        );

        return $pdf->stream();

    }

    /**
     * @param campagne|campagneCategory $campagne
    */

    public function save_flyer_pdf($campagne, $data) 
    {

        $pdf = App::make('dompdf.wrapper');

        $pdf->setOptions([
            'enable_php'           => true,
            'isRemoteEnabled'      => true, 
            'isHtml5ParserEnabled' => true, 
        ]);

        $pdf->loadView(
            'flyer', [
                'builder' => (new page_builder),
                'data'    => $data
            ]
        );

        $file_path = '/' . 'mail-files/' . $campagne->id . '_flyer.pdf';

        $pdf->save(Storage::path('public') . $file_path);

        $campagne->update([
            'urlflyer' => $file_path
        ]);

        return response()->json($file_path, 200);
    }

    /**
     * @param campagne|campagneCategory $campagne
    */
    public function save_campagne_pdf_file($path, $data) 
    {
        $pdf = App::make('dompdf.wrapper');

        $pdf->setOptions([
            'enable_php'           => true,
            'isRemoteEnabled'      => true, 
            'isHtml5ParserEnabled' => true, 
        ]);

        $pdf->loadView(
            'product', [
                'builder' => (new page_builder),
                'data'    => $data
            ]
        );

        $pdf->save(Storage::path('public') . $path);
    }


    public function stream_flyer_pdf(Campagne $campagne) 
    {

        $pdf = App::make('dompdf.wrapper');

        $pdf->setOptions([
            'enable_php'           => true,
            'isRemoteEnabled'      => true, 
            'isHtml5ParserEnabled' => true, 
        ]);

        $pdf->loadView(
            'flyer', [
                'builder' => (new page_builder),
                'data'    => (new CompagneController)->fields_Pdf($campagne->id)
            ]
        );

        return $pdf->stream();

    }

    public function validate_and_send_email(Request $request, Campagne $campagne) 
    {

        $this->generate_mail_csv_and_store($campagne);
        $this->save_flyer_pdf($campagne, $this->fields_Pdf($campagne->id));
        $this->save_letter_pdf($request, $campagne);    

        $emails = $this->get_settings_email();
        $emails = explode(',', $emails);
        foreach($emails as $mail) 
        {
            Notification::route('mail', $mail)
            ->notify(new CourierNotification($campagne));
        }

        $request->user()->notify(new CourierNotification($campagne));

        $campagne->update([
            'status' => 'CAMPAGNE ENVOYEE'
        ]);

        return response()->json("Email sent");

    }

    private function get_settings_email()
    {
        return DB::table('settings')->where('key', 'lcdt.email_imprimeur')->first()->value;
    }


    public function generate_mail_csv_and_store(Campagne $campagne) 
    {
        $campagne_cible = $campagne->campagneCible;
        $affiliate = $campagne->affiliate;
        
        $headers = [
            'N_customer',
            'Enterprise',
            'Prenom',
            'Nom',
            'adresse1',
            'adresse2',
            'Code Postal',
            'Ville',
            'TÃ©lÃ©phone',
            'email',
            'Affilie',
            'Prenom Directeur',
            'Nom Directeur',
            'adresse1',
            'adresse2',
            'Code Postal',
            'Ville'
        ];

        $data = [];

        $filename = $campagne->id;
        $current_date_time = Carbon::now()->timestamp;
        $csv_filename = $filename . "_" . date("Y-m-d", time()) . "_" . $current_date_time . ".csv";

        $file = fopen(storage_path('/app/public/mail-files/') . $csv_filename, 'w');
        fputcsv($file, $headers);

        foreach($campagne_cible as $cible) 
        {

            $data = array(
                $cible->customer_id,
                $cible->company,
                $cible->firstname,
                $cible->name,
                $cible->address1,
                $cible->address2,
                $cible->postcode,
                $cible->city,
                $cible->phone,
                $cible->email,
                $affiliate->raisonsociale,
                $affiliate->name,
                $affiliate->name,
                $affiliate->address,
                $affiliate->address2,
                $affiliate->postcode,
                $affiliate->city
            );
    
            fputcsv($file, $data);

        }

        $file_path = '/mail-files' . '/' . $csv_filename;

        $campagne->update([
            'urlcsv' => $file_path
        ]);

    }

    public function download_resource_file(Request $request)
    {
        $path = ltrim($request->resource, '/');
        $filename = $request->filename;
        return response()->download(Storage::path('public/' . $path), $filename);
    }


    /**
     * Get campagnes for a specefic compagne
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCampagnes()
    {

        $campagnes = Campagne::get();

        if($campagnes) {
            return response()->json([
                'campagnes' => $campagnes,
            ]);
        }else {
            return response()->json([
                'error' => 'error 400 : can\'t get compagnes'
            ], 400);
        }

    }

    /**
     * Get compagne cible
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCampagneCible()
    {

        $compagnesCible = CompagneCible::get(['id','industrie', 'statut']);
        dd($compagnesCible);
        if($compagnesCible) {
            return response()->json([
                'industrie' => $compagnesCible->industrie,
                'statut'    => $compagnesCible->statut,
            ]);
        }else {
            return response()->json([
                'error' => 'error 400 : can\'t get compagnes cibles'
            ], 400);
        }

    }

    /**
     * Get customer Statut / code Naf
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCustomerStatut()
    {

        $statuts = DB::table('customer_statut')->get();
        $codeNaf = DB::table('customer_naf')->get();
        $contacts = DB::table('contacts')->join('customers','customers.id','=','contacts.customer_id')->get(['email','naf','type']);
        $count = count($contacts);
        foreach ($statuts as $key => $statut) {
            $array_status[]=$statut-> name;
        }

        foreach ($codeNaf as $key => $code) {
            $array_codeNaf[]=$code-> selection;
        }

        if($contacts) {
            return response()->json([
                'customerStatut' => $array_status,
                'codeNaf'        => $array_codeNaf,
                'contacts'  => $contacts,
                'count' => $count,
            ]);
         }else {
            return response()->json([
                'error' => 'error 400 : can\'t get informations from DB'
            ], 400);
        }

    }
    /**
     * Get compagne categories
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCampagneCategory()
    {
        $campagnesCategory = DB::table('campagne_category')->get();
        $data_id = DB::table('campagne_category')->select()->get();

        foreach ($data_id as $key => $data) {
            $array_ids[]=$data->categorie_parent_id;
        }

        foreach ($campagnesCategory as $key => $data) {
            $type[]=$data->type;
        }

        if($campagnesCategory) {
            return response()->json([
                'campagnes' => $campagnesCategory->where('categorie_parent_id','0'),
                'my_ids' => $array_ids,
                'type' => $type,
            ]);
        }else {
            return response()->json([
                'error' => 'error 400 : can\'t get compagnes category'
            ], 400);
        }

    }
    /**
     * Get templates
     *
     * @return \Illuminate\Http\JsonResponse
     */
     public function getTemplates($id)
    {
        $mydata = DB::table('campagne_category')->select()->where('categorie_parent_id',"!=", '0')->get(); //get all sous-category
        $campagnesCategory = DB::table('campagne_category')->where('id',"=", $id)->get(); //get data by id
        $template = DB::table('campagne_category')->select()->where('categorie_parent_id',"=", $id)->get(); // get sous-category1

        // return $campagnesCategory;
        $data_id = DB::table('campagne_category')->select()->get();
        foreach ($data_id as $key => $data) {
            $array_ids[]=$data->categorie_parent_id;
        }
        foreach ($template as $key => $data) {
            $template_id[]=$data->id;
        }
        foreach ($campagnesCategory as $key => $data) {
            $type[]=$data->type;
        }

        if($template) {
            return response()->json([
                'campagnesCategory' =>$campagnesCategory,
                'id'=> $id,
                'template' => $template,
                'data_id' => $array_ids,
                'myvar' =>true,
                'type' =>$type,


                // 'template_id' => $template_id
            ]);
        }else {
            return response()->json([
                'error' => 'error 400 : can\'t get templates'
            ], 400);
        }

    }
    /**
     * Get getContentImage
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getContentImage($id)
    {
        $data = DB::table('campagne_category')->where('id',"=", $id)->get();
        foreach ($data as $key => $dataname) {
            $ss = $dataname->imagetemplate;
        }
        return $ss;
    }
     /**
     * Get SubCategory
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function SubCategory($id)
    {
        $mydata = DB::table('campagne_category')->select()->where('categorie_parent_id',"!=", '0')->get(); //get all sous-category
        $campagnesCategory = DB::table('campagne_category')->where('id',"=", $id)->get(); //get data by id
        $template = DB::table('campagne_category')->select()->where('categorie_parent_id',"=", $id)->get(); // get sous-category1
        foreach ($mydata as $key => $data) {
            $array_ids[]=$data->id;
        }

        foreach ($campagnesCategory as $key => $data) {
            $type[]=$data->type;
        }

        if ( (in_array($id, $array_ids)) && $template) {
            return response()->json([
                'campagnesCategory'=>$campagnesCategory,
                'data' =>$template,
                'mylist'=> $array_ids,
                'id' => $id,
                'myvar' =>true,
                'type' =>$type
            ]);
        }
        else {
            return response()->json([
                'error' => 'error 400 : Sub-Category does not exist'
            ], 400);
        }

    }

     /**
     * Get categories compagne
     *  @param  string  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function campagne_id($id)
    {
        $campagnesCategory = DB::table('campagne_category')->where('id',"=", $id)->get();
        if($campagnesCategory) {
            return response()->json([
                'campagnesCategory' =>$campagnesCategory,
            ]);
        }else {
            return response()->json([
                'error' => 'error 400 : can\'t get templates'
            ], 400);
        }
    }


         /**
     * Get categories compagne
     *  @param  string  $campagne_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCampagneCampagneCategory($campagne_id)
    {

        $campagne=Campagne::find($campagne_id);
        if($campagne==null)
         return response()->json('Campagne invalide', 509);

         $campagne_category=CampagneCategory::find($campagne->campagne_category_id);

         if($campagne_category==null)
         return response()->json('Campagne category '.$campagne->campagne_category_id.' not found', 509);
     
            return response()->json([
                'campagnesCategory' =>$campagne_category,
            ]);
      
    }


     /**
     * Get ciblesum (somme des contacts de chaque selection & type)
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function ciblesum(Request $request)
    {
        $t= intval($request->data)+1;
       $string= $request->data2.'_'.$t;
        $data2 = $request->data2;
        switch ($request->data) {
            case 0:
                $data_test = 'Cible' ;
                break;
             case 1:
                $data_test = 'Contact' ;
                break;
                 case 2:
                $data_test = 'Suspect' ;
                break;
                 case 3:
                $data_test = 'Prospect' ;
                break;
                 case 4:
                $data_test = 'Client' ;
                break;
                 case 5:
                $data_test = 'Fiche obsolete' ;
                break;
                 case 6:
                $data_test = 'Fiche doublons' ;
                break;
            default:
                $data_test ='';
                break;
        }

        $contacts = DB::table('contacts')->select('contacts.*')
        ->join('customers','customers.id','=','contacts.customer_id')
                                        ->join('customer_naf','customer_naf.code','=','customers.naf')
                                        ->where('contacts.type' ,"=", $data_test)
                                        ->where('customer_naf.selection' ,"=", $data2)->get();

        if($contacts){
            $count = $contacts->count();
            return response()->json([
                'contacts'=>$contacts,
                'count' => $count,
                'index' => $string,
            ]);

        }else {
            return response()->json([
                'error' => 'error 400 : can\'t get count'
            ], 400);
        }

    }

    /**
     * Get categories compagne
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function datarequest()
    {
        $codes = DB::table('customer_naf')->select('selection')->get();
        $types = DB::table('customer_statut')->get();
        $contacts = DB::table('contacts')->join('customers','customers.id','=','contacts.customer_id')->get(['email','naf','type']);
        foreach ($codes as $key => $code) {
            $array_codes[]=$code->selection;
        }
        foreach ($types as $key => $type) {
            $array_types[]=$type->name;
        }
        foreach ($contacts as $key => $contact) {
            $array_contacts_type[]=$contact->type;
            $array_contacts_code[]=$contact->naf;

        }
        return $contacts;
                 return response()->json([
                'array_codes' => $array_codes,
                'array_types' => $array_types,
                'contcts' => $contacts,
                'type' => $array_contacts_type,
                'code' => $array_contacts_code
            ]);

    }

     /**
     * Get categories compagne
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendEmailReminder(Request $request)
    {
        $user = 'farah-balti@live.com' ;

        Mail::send('welcome', ['user' => $user], function ($m) use ($user) {
            $m->from('farah.balti@esprit.tn', 'Your Application');

            $m->to($user)->subject('Your Reminder!');
        });
    }

     /**
     * Get categories compagne
     *
     * @return \Illuminate\Http\JsonResponse
     */
    function getStatusName() {
        $statuts = DB::table('customer_statut')->get();

        foreach ($statuts as $key => $statut) {
            $array_status[]=$statut-> name;
        }
        return response()->json([
            'array_status' => $array_status,

        ]);
    }

     /**
     * Get cibles compagne
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCampagny_Cible()
    {
        $cibles = DB::table('campagne_cible')->get();
        foreach ($cibles as $key => $cible) {
            $array_cibles_indus[]=$cible->industrie;
        }
        foreach ($cibles as $key => $cible) {
            $array_cibles_status[]=$cible->statut;
        }
        $codes = DB::table('customer_naf')->get();
        $types = DB::table('customer_statut')->get();

        foreach ($codes as $key => $code) {
            $array_codes[]=$code->selection;
        }
        foreach ($types as $key => $type) {
            $array_types[]=$type->name;
        }


        for ($i = 0; $i < count($cibles); $i++) {
            if( (in_array($array_types[$i], $array_cibles_status)) && (in_array($array_codes[$i], $array_cibles_indus)) ) {
                return 'exist';
            }else{
                return 'no';
            }
        }

        if($cibles) {
            return response()->json([
                'cibles' =>$cibles,
                'array_cibles_indus' =>$array_cibles_indus,
                'array_cibles_status' =>$array_cibles_status,
            ]);
        }else {
            return response()->json([
                'error' => 'error 400 : can\'t get data'
            ], 400);
        }

    }

     /**
     * Get cibles of a specific compagne
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCibleByCampagne($id)
    {
        $cibles = DB::table('campagne_cible')->join('customers','customers.id','=','campagne_cible.customer_id')
                                            ->join('contacts','contacts.customer_id','=','customers.id')
                                            ->where('campagne_cible.campagne_id',"=", $id)->get
                                            ([
                                                'campagne_cible.id',
                                                'campagne_cible.email',
                                                'campagne_cible.phone',
                                                'campagne_cible.postcode',
                                                'campagne_cible.address1',
                                                'campagne_cible.address2',
                                                'campagne_cible.name',
                                                'campagne_cible.firstname',
                                                'campagne_cible.company',
                                                'customers.gender',
                                                'contacts.type',
                                            ]);

        if($cibles) {
            $cmp = DB::table('campagnes')->where('id', $id)
            ->update([
                'nb' =>   $cibles->count()
            ]);
            return response()->json([
                'cibles' => $cibles,
                'count' => $cibles->count(),
                '$cmp' => $cmp
            ]);
        }else {
            return response()->json([
                'error' => 'error 400 : can\'t get data'
            ], 400);
        }
    }

     /**
     * create a new compagne the compagne cible
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function creatCompagne(Request $request)
    {
        $user = Auth::user();
      
        $campagne_category_id = $request->for_template;

        $campagne_category = CampagneCategory::find($campagne_category_id);
        $name=$campagne_category->name.'-';
                                            
        if($campagne_category==null)
        return response('Campagne category id '.$campagne_category_id.' not found',509);
       
        $addCompagne =  DB::table('campagnes')->insertGetId
        ([
            'datefinvalidatiom' => date("Y-m-d"),
            'datelancement' => date("Y-m-d"),
            'user_id' => $user->id,
            'affiliate_id' => $user->affiliate->id,
            'email_template_id' => intval($campagne_category->idapi),
            'campagne_category_id'=>$campagne_category->id,
            'address' => null,
            'expediteur' => null,
            'phone' => null,
            'lettreaccompagnement' => null,
            'type' => 'EMAIL',
            'run' =>  date("Y-m-d"),
            'montant' => 0,
            'nb' => $request->count,
            'name' => $name,
            'created_at' =>date("Y-m-d"),
            'updated_at' =>date("Y-m-d"),
        ]);
        DB::table('campagnes')->where('id','=',$addCompagne)->update([
            'name'=>$name.$addCompagne.'_'.date('Ymd')
        ]);
        $GLOBALS = $addCompagne;

        for ($i=0; $i <count($request->data) ; $i++) {

            $type = $request->data[$i]['type'];
            $status = $request->data[$i]['status'];
            switch ($type) {
                case 0:
                $data_test = 'Cible' ;
                break;
                 case 1:
                $data_test = 'Contact' ;
                break;
                 case 2:
                $data_test = 'Suspect' ;
                break;
                 case 3:
                $data_test = 'Prospect' ;
                break;
                 case 4:
                $data_test = 'Client' ;
                break;
                 case 5:
                $data_test = 'Fiche obsolete' ;
                break;
                 case 6:
                $data_test = 'Fiche doublons' ;
                break;
                default:
                $data_test ='';
                break;
            }
            $contacts = DB::table('contacts')->join('customers','customers.id','=','contacts.customer_id')
                                            ->join('customer_naf','customer_naf.code','=','customers.naf')
                                            ->where('contacts.type' ,"=", $data_test)
                                            ->where('customer_naf.selection' ,"=", $status)->get(
                                                ['contacts.id',
                                                'contacts.customer_id',
                                                'customer_naf.selection',
                                                'contacts.type',
                                                'contacts.mobile',
                                                'contacts.email',

                                            ]);


            if($contacts->count() != 0){

                $contacts->each(function ($item ) {

                    DB::table('campagne_cible')->insert
                    ([
                        'affiliate_id' => 1,
                        'cible_statut_id' => 1,
                        'address_id' => 1,
                        'customer_id' => $item->customer_id,
                        'contact_id' => $item->id,
                        'campagne_old_id' => 0,
                        'campagne_id' => $GLOBALS,
                        'industrie' =>  $item->selection,
                        'statut' => $item->type,
                        'phone' => $item->mobile,
                        'email' => $item->email,
                        'firstname'=>$item->firstname,
                        'gender'=>$item->gender,
                        'name'=>$item->name,
                        'created_at' => date("Y-m-d"),
                        'updated_at' => date("Y-m-d"),
                    ]);

                });

            }else continue;
        }

        $data_old = $request->data_old;
        $test_exist =[];
        for ($i=0; $i < count($data_old); $i++) {
            $contacts_old = DB::table('campagne_cible')->where('campagne_cible.campagne_old_id' ,"=", $data_old[$i])
                                ->get();

            $contacts_old->each(function ($item ) {
                $user_exist = DB::table('campagne_cible')->where('campagne_cible.campagne_id','=',$GLOBALS)
                                                         ->where('campagne_cible.email','=',$item->email)->get();
                    $test_exist[] =$user_exist ;
                    if (!$user_exist) {

                        DB::table('campagne_cible')->insert
                        ([
                            'affiliate_id' => 1,
                            'cible_statut_id' => 1,
                            'address_id' => 1,
                            'customer_id' => $item->customer_id,
                            'contact_id' => $item->contact_id,
                            'campagne_old_id' => 0,
                            'campagne_id' => $GLOBALS,
                            'industrie' =>  $item->industrie,
                            'statut' => $item->statut,
                            'phone' => $item->phone,
                            'email' => $item->email,
                               'firstname'=>$item->firstname,
                           'gender'=>$item->gender,
                        'name'=>$item->name,
                            'created_at' => date("Y-m-d"),
                            'updated_at' => date("Y-m-d"),
                        ]);
                    }

                });
        }
        return response()->json([
                'GLOBALS' => $GLOBALS,
                'test_exist' => $test_exist,
                'categ_id'=> $campagne_category_id,
            ]);

    }

    /**
     * Get compagne cible when returns from segmentations component
     *  @param  string  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCompgneCibleSelected($id)
    {
        $cibles = DB::table('campagne_cible')
        ->where('campagne_id' ,"=", $id)
        ->where('deleted_at','=',NULL)
        ->groupBy('industrie')
        ->groupBy('statut')
        ->get([
            'industrie',
            'statut',
        ]);

        if($cibles) {
            $array_cible = [];
            $string = '';
            foreach ($cibles as $key => $cible) {
                switch (strtolower($cible->statut)) {
                    case 'cible':
                    $data_test = 1 ;
                    break;
                    case 'contact':
                    $data_test = 2 ;
                    break;
                    case 'suspect':
                    $data_test = 3 ;
                    break;
                    case 'prospect':
                    $data_test = 4 ;
                    break;
                    case 'client':
                    $data_test = 5 ;
                    break;
                    case 'fiche obsolete':
                    $data_test = 6 ;
                    break;
                    case 'fiche doublons':
                    $data_test = 7 ;
                    break;
                }
                $string = [
                    'secteur'=>$cible->industrie,
                    'type'=> $data_test
                ];
                $array_cible[] = $string;
            };
            // get count after updating list from segmentation view
            $count_retourn = DB::table('campagnes')->where('id' ,"=", $id)->get('nb');

            return response()->json([
                'cibles' => $array_cible,
                'count' => $count_retourn,
            ]);
        }else {
            return response()->json([
                'error' => 'error 400 : can\'t get templates'
            ], 400);
        }
    }

    /**
     * Get compagne cible when returns from segmentations component
     *  @param  string  $id
     *  @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function contentform(Request $request, $id)
    {

        $c=Campagne::find($id);
        $c->email=$request->post('email');
        $c->phone=$request->post('phone');
        $c->save();

        return response()->json([
            'ok' => 1
        ]);

    }

    /**
     * Get compagne cible when returns from segmentations component
     *  @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteCompagneCible(Request $request)
    {
        $cmp = DB::table('campagne_cible')->where('id', $request->id)
        ->where('campagne_id', intval($request->campagne_id))
        ->update([
                'deleted_at' =>  date("Y-m-d"),
        ]);

        $cible = DB::table('campagne_cible')->where('campagne_id',"=", $request->campagne_id)
                                                ->where('deleted_at',"=", NULL)->get();
        $cmp = DB::table('campagnes')->where('id', intval($request->campagne_id))
        ->update([
            'nb' =>   $cible->count()
        ]);

        return response()->json([
            'cibles' => $cible->count(),
        ]);

    }

    /**
     * Get compagne cible when returns from segmentations component
     *  @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function insertCompagneCible(Request $request)
    {
        $cmp = DB::table('campagne_cible')->where('id',  $request->id)
        ->where('campagne_id', intval($request->campagne_id))
        ->update([
                'deleted_at' =>  NULL,
        ]);
        $cible = DB::table('campagne_cible')->where('campagne_id',"=", $request->campagne_id)
                                                ->where('deleted_at',"=", NULL)->get();
        $cmp = DB::table('campagnes')->where('id', intval($request->campagne_id))
        ->update([
            'nb' =>   $cible->count()
        ]);

        return response()->json([
            'cibles' => $cible->count(),
        ]);


    }

    /**
     * getCourrier
     * @param  String  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCourrier()
    {
        $Courrier = DB::table('campagnes')->where('type', "=",'COURRIER')->where('name', "=",'Flyer')->get();
        $Courrier_lettre = DB::table('campagnes')->where('type', "=",'COURRIER')->where('name', "=",'lettre accompagnement')->get();
        $Courrier_autre = DB::table('campagnes')->where('type', "=",'COURRIER')->where('name', "!=",'Flyer')->where('name', "!=",'lettre accompagnement')->get();

        return response()->json([
            'data' => $Courrier,
            'data1' => $Courrier_lettre,
            'data2' => $Courrier_autre,
        ]);

    }

    /**
     * getOldCompagne
     * @return \Illuminate\Http\JsonResponse
     */
    public function getOldCompagne()
    {

        $data = DB::table('campagnes')->join('campagne_cible','campagnes.id','=','campagne_cible.campagne_id')
                    ->where('campagne_old_id','!=','0')
                    ->distinct()
                    ->get(['campagne_old_id','campagnes.name','campagnes.created_at','nb','campagne_cible.campagne_id']);
        $cmp_id = [];


        if( $data ) {
            $cibles = [];
            $grouped_cibles = [];
            $mapped_old_id = [];

            foreach($data as $k=>$v){
                if(!in_array($v->campagne_id,$cmp_id)){
                    array_push($cmp_id,$v->campagne_id);
                }
                $mapped_old_id[$v->campagne_old_id] = $v->campagne_id;
            }


            $cibles = DB::table('campagne_cible')->whereIn('campagne_id',$cmp_id)->get()->toArray();


            foreach($cibles as $k=>$v){
                $grouped_cibles[$v->campagne_id][] = $v->email;
            }

            return response()->json([
                'oldCompagne' => $data ,
                'map_old_id'=>$mapped_old_id,
                'old_cibles'=>$grouped_cibles,
            ]);
        }else {
            return response()->json([
                'error' => 'error 400 : can\'t get data'
            ], 400);
        }
    }

    /**
     * getOldCompagne
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteOldCompagne(Request $request)
    {
        $data = DB::table('campagne_cible')->where('campagne_id', $request->id)
        ->update([
            'campagne_old_id' => 0
        ]);
        $oldCompagne = DB::table('campagnes')->join('campagne_cible','campagnes.id','=','campagne_cible.campagne_id')
                    ->where('campagne_old_id','!=','0')
                    ->distinct()
                    ->get(['campagnes.id','campagnes.name','campagnes.created_at','campagnes.nb']);
        if( $request->id ) {

            return response()->json([
                'data' => $request->id ,
                'oldCompagne' => $oldCompagne ,
            ]);
        }else {
            return response()->json([
                'error' => 'error 400 : can\'t get data'
            ], 400);
        }
    }

    /**
     * Ganarate csv file for compagne cibles
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function exportCsv($id)
    {

        $data_id = DB::table('campagne_cible')->select()->where('campagne_id',$id)->get();
        $filename="LCDT";
        $current_date_time = Carbon::now()->timestamp;
        $csv_filename = $filename."_".date("Y-m-d",time())."_".$current_date_time.".csv";

        $headers = array(
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$csv_filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0",
            "file"                => $csv_filename,
        );

        $columns = array('Email', 'Company', 'Firstname', 'Name', 'Address2' , 'Address1', 'Postcode', 'City', 'Industrie', 'Phone' );
        $callback = function() use($data_id, $columns, $csv_filename ) {
            $file = fopen('../public/uploads/'.$csv_filename, 'w');

            fputcsv($file, $columns);

            foreach ($data_id as $data) {

                $row['Email']  = $data->email;
                $row['Company']    = $data->company;
                $row['Firstname']    = $data->firstname;
                $row['Name']  = $data->name;
                $row['Address2']  = $data->address2 ;

                $row['Address1']  = $data->address1;
                $row['Postcode']    = $data->postcode;
                $row['City']    = $data->city;
                $row['Industrie']  = $data->industrie;
                $row['Phone']  = $data->phone   ;

                $data_file = array($row['Email'], $row['Company'], $row['Firstname'], $row['Name'], $row['Address2'] , $row['Address1'], $row['Postcode'], $row['City'], $row['Industrie'], $row['Phone']) ;


                fputcsv($file, $data_file);

            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Ganarate pdf file for lettre d'accopagnement
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function downloadPdf(Request $request)
    {

        $pdf_filename = $request->fileName;

        $this->fpdf = new \Mpdf\Mpdf([
             'default_font_size' => 10,
             'default_font' => 'Arial'

        ]);
        $this->fpdf->AddPage();
        $this->fpdf->showImageErrors = true;
        $this->fpdf->img_dpi = 96;

        $this->fpdf->Image('../public/images/logolcdt.png' , 15, 15, 100, 20);
        $this->fpdf->Ln(15);


        $htmlFooter = '<div style= "width: 100%;
        display: flex;
        justify-content: flex-end;
        background-color: orangered;
        margin-top: auto;
        height: 10px;
        padding: 0 40px 0 0;
        margin-bottom: 20px;">

        <div style = "
        padding: 0 3px;
        font-size: xx-small;
        font-weight: bold;
        margin-left: 400px;
        width: 350px;
        background-color: white;
        margin-top: -1px;">
         <span>LE RESEAU EXPERT </span>
         <span style="color:orangered;">DES TOITS PROFESSIONNELS</span>
        </div>
        </div>';
        $this->fpdf->SetHTMLFooter($htmlFooter);


        $this->fpdf->SetFont('Arial','B',10);
        $this->fpdf->Cell(100,5, $request->expediteur );
        $this->fpdf->SetFont('Arial','',10);
        $this->fpdf->Cell(200,5,"SARL");
        $this->fpdf->Ln(8);

        $this->fpdf->SetFont('Arial','',10);
        $this->fpdf->Cell(100,5,$request->expediteur2);
        $this->fpdf->SetFont('Arial','',10);
        $this->fpdf->Cell(300,5,"Civilité Prénom Nomn");
        $this->fpdf->Ln(8);


        $this->fpdf->SetFont('Arial','',10);
        $this->fpdf->Cell(100,5,$request->address);
        $this->fpdf->SetFont('Arial','',10);
        $this->fpdf->Cell(300,5,"310 Route de l'Odyssée");
        $this->fpdf->Ln(8);

        $this->fpdf->SetFont('Arial','',10);
        $this->fpdf->Cell(100,5,$request->address2);
        $this->fpdf->SetFont('Arial','',10);
        $this->fpdf->Cell(300,5,"45700 CONFLANS SUR LOING");
        $this->fpdf->Ln(8);

        $this->fpdf->SetFont('Arial','',10);
        $this->fpdf->Cell(100,5,$request->coord);
        $this->fpdf->Ln(8);

        $this->fpdf->SetFont('Arial','',10);
        $this->fpdf->Cell(100,5, $request->email);
        $this->fpdf->SetFont('Arial','',10);

        $this->fpdf->Ln(10);


        $this->fpdf->Cell(120,5,);
        $this->fpdf->SetFont('Arial','',10);
        $this->fpdf->Cell(200,5,"Chilly Mazarin, le XX juin 2021");


        $this->fpdf->Ln(15);
        $this->fpdf->SetFont('Arial','',10);
        $this->fpdf->Cell(40,5,'Madame X, Monsieur Y, Madame, Monsieur,');
        if (!$request->html) {
            $request->html = '<div></div>';
        }
        $this->fpdf->Ln(15);
        $this->fpdf->SetFont('Arial','',10);
        $this->fpdf->WriteHTML( $request->html);

        $this->fpdf->Ln(10);
        $this->fpdf->Cell(120,5,);
        $this->fpdf->SetFont('Arial','B',10);
        $this->fpdf->Cell(200,5,"L'equipe commerciale");

        $this->fpdf->Ln(8);
        $this->fpdf->Cell(120,5,);
        $this->fpdf->SetFont('Arial','',10);
        $this->fpdf->Cell(200,5,"Agence X");



        $this->fpdf->Output('../public/uploads/'.$pdf_filename, 'F');

        return response()->json([
            'message' => 'yess is done !!',
            'fileName' => $pdf_filename

        ]);

    }

    /**
     * Get all compagnes Sarbacane
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function insertdestinataire(Request $request , $id){
 
        $test_contacts=$request->get('data');
    
        $campagne=Campagne::find($id);
        $uid=uniqid();
        $data = $this->sarbacane()->post('lists', ["name"=>str_replace(' ','_','List_contact_test'. '_' .$campagne->name.'_'.$uid)]);
        $id_list = $data['id'];
        //Permet d'ajouter un champ (une colonne) à une liste.
        //Permet d'ajouter un contact à une liste.
        $this->sarbacane()->addCampagneFieldsAndContacts($id_list,$id,$test_contacts);

        $template = CampagneCategory::find($campagne->campagne_category_id);
        //Permet de créer une campagne E-mail
        $send = $this->sarbacane()->post('campaigns/email', [

                "name"=>  $campagne->name.'_test_'.$uid,
                "emailFrom"=>$campagne->affiliate->reponseaddress,
                "aliasFrom"=> 'La compagnie des toits - '.$campagne->affiliate->name,
                "emailReplyTo"=>$campagne->affiliate->reponseaddress,
                "aliasReplyTo"=> 'La compagnie des toits - '.$campagne->affiliate->name,
                "subject"=> $campagne->campagneCategory->objet,
        
        ]);

        if($send){
            $camp_id= $send['id'] ;
             //Permet d'importer la copie d'un modèle dans la campagne.
            $this->sarbacane()->post('campaigns/'.$camp_id.'/content', [ "templateId"=> $template->idapi]);
            //Permet d'importer la copie d'une liste de contacts dans la campagne.
            $contact_list = $this->sarbacane()->post('campaigns/'.$camp_id.'/list', ["listId"=> $id_list]);
        }
        if ($contact_list) {
            $camp_id= $send['id'] ;
             //Permet d'envoyer (ou de programmer) une campagne.
            $this->sarbacane()->post('campaigns/'.$camp_id.'/send');
        }
        return  response()->json('Envoi TEST campagne '.$campagne->name. ' réussie.');
    }

    /**
     * Get all compagnes Sarbacane
     * @param $data sata
     * @param $data_csv data_csv
     * @return \Illuminate\Http\JsonResponse
     */
    public function envoi_lettre($data, $data_csv){
        $user = auth()->user();
        Mail::to($user->email)->send(new MyDemoMail($data, $data_csv));
        return "email sent";
    }

    /**
     * Get all compagnes Sarbacane
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $response = Http::withHeaders([
            'accountId'   => '61a62892e924e35ea3f5469e',
            'apiKey'    => 'gCdU9nAUQj6THBlujWyMvA',
        ])->get('https://sarbacaneapis.com/v1/campaigns');

        return $response;
    }

    /**
     * send compagne immediat
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    // public function envoi($id)
    // {
    //     $user = auth()->user();
    //     //get cible
    //     $cible = DB::table('campagne_cible')->where('campagne_id',"=", $id)
    //                                         ->where('deleted_at',"=", NULL)
    //                                         ->get();
    //     // return $cible;
    //     foreach ($cible as $key => $code) {
    //         $array_cible[] =['email' => $code->email];
    //     } ;

    //     $template = DB::table('campagne_category')->where('id',"=", $id)->get();
    //     foreach ($template as $key => $code) {
    //         $array_temp =$code->idapi;
    //     };

    //     foreach ($template as $key => $code) {
    //         $array_name =$code->name.'-immédiat-'.$number;
    //     } ;

    //     // $data_template = DB::table('email_template')->where('id',"=", $array_temp)->get();
    //     // foreach ($data_template as $key => $code) {
    //     //     $temp =$code->idapi;
    //     // } ;

    //     $formatedDate = date("Y-m-d H:i:s");
    //     //creation d'une compagne
    //     $send = Http::withHeaders([
    //         'accountId'=> '61a62892e924e35ea3f5469e',
    //         'apiKey'=> 'gCdU9nAUQj6THBlujWyMvA'
    //     ])->post('https://sarbacaneapis.com/v1/campaigns/email', [

    //             "name"=> $array_name,
    //             "emailFrom"=>$request->emailFrom,
    //             "aliasFrom"=> $request->emailFrom,
    //             "emailReplyTo"=>$request->emailFrom,
    //             "aliasReplyTo"=> $request->emailFrom,
    //             "subject"=> $request->subject,
    //             "preheader"=> "Preheader",
    //     ]);

    //     if($send){
    //         //ajout du model
    //         $camp_id= $send['id'] ;
    //         $template_data = Http::withHeaders([
    //             'accountId'=> '61a62892e924e35ea3f5469e',
    //             'apiKey'=> 'gCdU9nAUQj6THBlujWyMvA'])
    //         ->post('https://sarbacaneapis.com/v1/campaigns/'.$camp_id.'/content', [

    //                 "templateId"=> $array_temp

    //         ]);
    //     }
    //         //ajout destinataires
    //     $camp_id= $send['id'] ;
    //     $response = Http::withHeaders([
    //         'accountId'=> '61a62892e924e35ea3f5469e',
    //         'apiKey'=> 'gCdU9nAUQj6THBlujWyMvA'
    //     ])->post('https://sarbacaneapis.com/v1/campaigns/'.$camp_id.'/recipients',
    //         $array_cible
    //     );

    //     if ($response) {
    //     $camp_id= $send['id'] ;
    //     $daysent = Http::withHeaders([
    //         'accountId'=> '61a62892e924e35ea3f5469e',
    //         'apiKey'=> 'gCdU9nAUQj6THBlujWyMvA'
    //         ])->post('https://sarbacaneapis.com/v1/campaigns/'.$camp_id.'/send', [
    //                 [
    //                     "requestedSendDate"=> $formatedDate
    //             ]
    //         ]);
    //     }

    //     return 'data added imme';
    // }
    /**
     * envoi compagne differer
     * @param string $id
     * @param \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function envoiprogramme(Request $request , $id)
    {
        
      if($request->date==""||$request->time==""){
          return response()->json('Erreur envoi différé. Veuillez préciser la date et l\'heure.',509);
      }
        $campagne=Campagne::find($id);
        $uid=uniqid();
     
        //Permet de créer une liste de contacts.
        $data = $this->sarbacane()->post('lists', [ "name"=>str_replace(' ','_','List_contact_diff'. '_' .$campagne->name.'_'.$uid)]);

        $id_list = $data['id'];
        //Permet d'ajouter un champ (une colonne) à une liste.
        //Permet d'ajouter un contact à une liste.
        $this->sarbacane()->addCampagneFieldsAndContacts($id_list,$id);
   /*
        $cible = DB::table('campagne_cible')->where('campagne_cible.campagne_id',"=", $id)->where('campagne_cible.deleted_at',"=", NULL)->get();

        /*foreach ($cible as $key => $code) {
            $array_cible[] =[
                "Email_contact" => $code->email,
                "email" => $code->email,
                "Nom_agence" => $request->input_agence,
                "Telephone_agence" => $request->input_phone,
                "Email_agence" => $request->input_email
                ] ;
        }
        //Permet d'ajouter un champ (une colonne) à une liste.
        $this->sarbacane()->post('lists/'.$id_list.'/fields', ["kind" => 'STRING',"caption" => 'Nom_agence']);
        $this->sarbacane()->post('lists/'.$id_list.'/fields', ["kind" => 'STRING',"caption" => 'Telephone_agence']);
        $this->sarbacane()->post('lists/'.$id_list.'/fields', ["kind" => 'STRING',"caption" => 'Email_agence']);

        for($i=0; $i<count($array_cible); $i++) {
            //Permet d'ajouter un contact à une liste.
        $data_added = $this->sarbacane()->post('lists/'.$id_list.'/contacts', $array_cible[$i] );
        }*/

        $template = CampagneCategory::find($campagne->campagne_category_id);
        
     
       //Permet de créer une campagne E-mail
        $send = $this->sarbacane()->post('campaigns/email', [

                "name"=> $campagne->name.'_diff_'.$uid,
                "emailFrom"=>$campagne->affiliate->reponseaddress,
                "aliasFrom"=> 'La compagnie des toits - '.$campagne->affiliate->name,
                "emailReplyTo"=>$campagne->affiliate->reponseaddress,
                "aliasReplyTo"=> 'La compagnie des toits - '.$campagne->affiliate->name,
                "subject"=> $campagne->campagneCategory->objet,
        
        ]);

        if($send){
            $camp_id= $send['id'] ;
            //Permet d'importer la copie d'un modèle dans la campagne.
            $template_data = $this->sarbacane()->post('campaigns/'.$camp_id.'/content', [ "templateId"=> $template->idapi ]);
            //Permet d'importer la copie d'une liste de contacts dans la campagne.
            $contact_list = $this->sarbacane()->post('campaigns/'.$camp_id.'/list', [  "listId"=> $id_list ]);
        }
        $formatedDate=Carbon::parse($request->date.' '.$request->time);
        if ($contact_list) {

            $camp_id= $send['id'] ;
            //Permet d'envoyer (ou de programmer) une campagne.
            $daysent = $this->sarbacane()->post('campaigns/'.$camp_id.'/send', [ "requestedSendDate"=> $formatedDate]);
        }
        return  response()->json('Envoi différé de la campagne '.$campagne->name. ' réussie.');

    }



    /**
     * Get cibles of a specific compagne
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCount_cible($id)
    {
        $count_cible = DB::table('campagne_cible')->where('campagne_cible.campagne_id',"=", $id)
                                                ->where('campagne_cible.deleted_at',"=", NULL)->get();

        if($count_cible) {

            return response()->json([
                'count_cible' =>$count_cible->count(),
            ]);
        }else {
            return response()->json([
                'error' => 'error 400 : can\'t get data'
            ], 400);
        }
    }
    /**
    * envoi campagne immediat
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function createdata(Request $request, $id){

        $campagne=Campagne::find($id);
        $uid=uniqid();
        $data = $this->sarbacane()->post('lists', ["name"=>str_replace(' ','_','List_contact_imme'. '_' .$campagne->name.'_'.$uid)]);
        $id_list = $data['id'];
        //Permet d'ajouter un champ (une colonne) à une liste.
        //Permet d'ajouter un contact à une liste.
        $this->sarbacane()->addCampagneFieldsAndContacts($id_list,$id);

        $template = CampagneCategory::find($campagne->campagne_category_id);
        //Permet de créer une campagne E-mail
        $send = $this->sarbacane()->post('campaigns/email', [

                "name"=>  $campagne->name.'_imme_'.$uid,
                "emailFrom"=>$campagne->affiliate->reponseaddress,
                "aliasFrom"=> 'La compagnie des toits - '.$campagne->affiliate->name,
                "emailReplyTo"=>$campagne->affiliate->reponseaddress,
                "aliasReplyTo"=> 'La compagnie des toits - '.$campagne->affiliate->name,
                "subject"=> $campagne->campagneCategory->objet,
        
        ]);

        if($send){
            $camp_id= $send['id'] ;
             //Permet d'importer la copie d'un modèle dans la campagne.
            $this->sarbacane()->post('campaigns/'.$camp_id.'/content', [ "templateId"=> $template->idapi]);
            //Permet d'importer la copie d'une liste de contacts dans la campagne.
            $contact_list = $this->sarbacane()->post('campaigns/'.$camp_id.'/list', ["listId"=> $id_list]);
        }
        if ($contact_list) {
            $camp_id= $send['id'] ;
             //Permet d'envoyer (ou de programmer) une campagne.
            $this->sarbacane()->post('campaigns/'.$camp_id.'/send');
        }
        return  response()->json('Envoi de la campagne '.$campagne->name. ' réussie.');
    }
    public function getTempname($id){
        $campagnesCategory = DB::table('campagne_category')->where('id',"=",$id)->first();
        if($campagnesCategory!=null)
        return $campagnesCategory->name;

        return null;
    }

    /**
     * Get imagefield
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function imagefield($id){
        $data = DB::table('campagne_category')->select()->where('id',"=", $id)->get();
        foreach ($data as $key => $code) {
            $array_data1 =[
                "xfield1" => $code->xfield1,
                "yfield1" => $code->yfield1,
                "color1" => $code->color1,
                ] ;
            $array_data2 =[
                "xfield2" => $code->xfield2,
                "yfield2" => $code->yfield2,
                "color2" => $code->color2,
                ] ;
            $array_data3 =[
                "xfield3" => $code->xfield3,
                "yfield3" => $code->yfield3,
                "color3" => $code->color3,
                ] ;
        }
        return response()->json([
            'data1' => $array_data1,
            'data2' => $array_data2,
            'data3' => $array_data3
        ]);
    }
     /**
     * Get lettredata
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function lettredata($id)
    {

        $campagne = Campagne::find($id);
        $campagne_category=CampagneCategory::find($campagne->campagne_category_id);

        $content = is_null($campagne->lettreaccompagnement) || $campagne->lettreaccompagnement == ''
        ? $campagne_category->lettreaccompagnement 
        : $campagne->lettreaccompagnement;

        return response()->json([
            'content' => $content,
            'email'   => $campagne->email,
            'phone'   => $campagne->phone  
        ]);

    }

    public function lettredata_pdf($id)
    {
        $campagne=Campagne::find($id);
        $campagne_category=CampagneCategory::find($campagne->campagne_category_id);


        $content = is_null($campagne->lettreaccompagnement) || $campagne->lettreaccompagnement == ''
        ? $campagne_category->lettreaccompagnement 
        : $campagne->lettreaccompagnement;

        return [
            'content' => $content,
        ];
    }

    public function fields_for_marketing(CampagneCategory $campagne, Request $request, $email = null, $telephone = null) 
    {

        $affiliate = $request->user()->affiliate;
        if(is_null($telephone)) $telephone = $affiliate->telephone;
        if(is_null($email)) $email = $affiliate->reponseaddress;

        if(is_null(json_decode($campagne->fields))) 
        {
            $fields = [];
            $fields['Telephone_agence']['value'] = $telephone;
            $fields['Telephone_agence']['active'] = 1;
            $fields['Email_agence']['value'] = $email;
            $fields['Email_agence']['active'] = 1;
            $fields['personalize'] = false;
            return $fields;
        }

        $fields = json_decode($campagne->fields);
        
        $fields->Nom_agence->value=$affiliate->name;
        $fields->Telephone_agence->value=$telephone;
        $fields->Email_agence->value=$email;
        $fields->Prenom_dirigeant->value=$affiliate->firstnamedirector;
        $fields->Nom_dirigeant->value=$affiliate->namedirector;
        $fields->Email_dirigeant->value=$affiliate->email;
        $fields->Portable_dirigeant->value=$affiliate->mobile;
        $fields->Adresse_agence->value=$affiliate->address . ' ' . $affiliate->address2;
        $fields->CP_agence->value=$affiliate->postcod;
        $fields->Ville_agence->value=$affiliate->city;
        $fields->Page_agence->value=$affiliate->urlagence;
        $fields->Linkedin_agence->value=$affiliate->linkedin;
        $filedepliant = json_decode($campagne->filedepliant);
        $fields->file_depliant=$filedepliant;
        $fields->personalize = true;

        return $fields;

    }


    public function fields($id)
    {
        
        $campagne = Campagne::find($id);

        $affiliate=$campagne->affiliate;
        $cc=$campagne->campagneCategory;
        $telephone = !is_null($campagne->phone) && $campagne->phone != '' ? $campagne->phone : $affiliate->telephone;
        $email = !is_null($campagne->email) && $campagne->email != '' ? $campagne->email : $affiliate->reponseaddress;
        $fields=json_decode($cc->fields);
        $fields->Nom_agence->value=$affiliate->name;
        $fields->Telephone_agence->value=$telephone;
        $fields->Email_agence->value=$email;
        $fields->Prenom_dirigeant->value=$affiliate->firstnamedirector;
        $fields->Nom_dirigeant->value=$affiliate->namedirector;
        $fields->Email_dirigeant->value=$affiliate->email;
        $fields->Portable_dirigeant->value=$affiliate->mobile;
        $fields->Adresse_agence->value=$affiliate->address.' '.$affiliate->address2;
        $fields->CP_agence->value=$affiliate->postcod;
        $fields->Ville_agence->value=$affiliate->city;
        $fields->Page_agence->value=$affiliate->urlagence;
        $fields->Linkedin_agence->value=$affiliate->linkedin;
        $filedepliant=json_decode($cc->filedepliant);
        $fields->file_depliant=$filedepliant;
      
        return response()->json(
            array(
                'fields' => $fields, 
                'image' => $cc->imagetemplate,
                'campagneCategory' => $cc
            )
        );

    }

    public function fields_Pdf($id) 
    {
        $c=Campagne::find($id);
      
        $affiliate=$c->affiliate;
        $cc=$c->campagneCategory;
        $telephone = !is_null($c->phone) && $c->phone != '' ? $c->phone : $affiliate->telephone;
        $email = !is_null($c->email) && $c->email != '' ? $c->email : $affiliate->reponseaddress;
        $fields=json_decode($cc->fields);
        $fields->Nom_agence->value=$affiliate->name;
        $fields->Telephone_agence->value=$telephone;
        $fields->Email_agence->value=$email;
        $fields->Prenom_dirigeant->value=$affiliate->firstnamedirector;
        $fields->Nom_dirigeant->value=$affiliate->namedirector;
        $fields->Email_dirigeant->value=$affiliate->email;
        $fields->Portable_dirigeant->value=$affiliate->mobile;
        $fields->Adresse_agence->value=$affiliate->address.' '.$affiliate->address2;
        $fields->CP_agence->value=$affiliate->postcod;
        $fields->Ville_agence->value=$affiliate->city;
        $fields->Page_agence->value=$affiliate->urlagence;
        $fields->Linkedin_agence->value=$affiliate->linkedin;
        $filedepliant=json_decode($cc->filedepliant);
        $fields->file_depliant=$filedepliant;
      
        return array(
            'fields'           => $fields,
            'image2'           => $cc->imagetemplate,
            'image'            => $cc->urlimageflyerpage1,
            'campagneCategory' => $cc
        );
    }


    public function downloadPdfFile(Request $request)
    {
        $path=$request->get('path');
        $filename=$request->get('filename');
        return response()->download(Storage::path('public/' . $path), $filename);
    }
}
