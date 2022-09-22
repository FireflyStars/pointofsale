<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

class CustomerController extends Controller
{
    //
    public function getListInfoForCustomer(){
        return response()->json([
            'customerOrigins'   => DB::table('customer_origins')->select('id as value', 'name as display')->orderBy('name')->get(),
            'status'            => DB::table('customer_statut')->select('id as value', 'name as display')->orderBy('name')->get(),
            'customerCats'      => DB::table('customer_categories')->select('id as value', 'name as display')->orderBy('name')->get(),
            'customerPentes'    => DB::table('customer_pente')->select('id as value', 'name as display')->orderBy('name')->get(),
            'nafs'              => DB::table('customer_naf')->select('code', 'name', 'selection')->orderBy('name')->get(),
            'taxes'             => DB::table('taxes')->select('id as value', 'name as display')->orderBy('id')->get(),
            'addressTypes'      => DB::table('address_type')->select('id as value', 'name as display')->orderBy('name')->get(),
            'customerQualites'  => DB::table('customer_qualite')->select('id as value', 'name as display')->orderBy('id')->get(),
            'customerTypeBatiments' => DB::table('customer_typebatiment')->select('id as value', 'name as display')->orderBy('id')->get(),
            'customerMateriaus' => DB::table('customer_materiau')->select('name as value', 'name as display')->orderBy('id')->get(),
            'customerPaiements' => DB::table('customer_paiement')->select('id as value', 'name as display')->orderBy('id')->get(),            
            'contactTypes'      => DB::table('contact_type')->select('id as value', 'name as display')->orderBy('id')->get(),
            'contactQualites'   => DB::table('contact_qualite')->select('id as value', 'name as display')->orderBy('id')->get(),            
        ]);
    }

    protected $siretBaseUrl  = 'https://api.insee.fr/entreprises/sirene/V3/siren/';
    protected $accessToken  = 'Bearer 57999085-b282-3bf5-92de-6fc6168cdf88';
    protected $clientKey    = 'VKSa7MH3ySsh61Y0XYKcJiWWfMoa';
    protected $clientSecret = 'yGpLpImxJZngqp6iCsXjnd4RSnMa';

    /**
     * get siret number and check if it is valid or not
     * 
     */
    public function checkSiret(Request $request){
        try {
            $client = new Client(['base_uri' => $this->siretBaseUrl]);
    
            $response = $client->request('GET', $request->siret, [
                'headers' => [
                    'Accept'     => 'application/json',
                    'Authorization'     => $this->accessToken,
                ]
            ]);
            $code = $response->getStatusCode();
            if ($code == 200) {
                $body   = $response->getBody();
                $content = json_decode($body->getContents());
                return response()->json([
                    'success'=> true,
                    'data'=> $content->uniteLegale->periodesUniteLegale[0],
                ]);
            }else if($code == 400){
                $errorMessage = 'Incorrect number of parameters or parameters are incorrectly formatted';
                return response()->json([
                    'success'   => false,
                    'error'     => $errorMessage,
                ]);                
            }else if($code == 401){
                $errorMessage = 'Missing or invalid access token';
                return response()->json([
                    'success'   => false,
                    'error'     => $errorMessage,
                ]);                                
            }else if($code == 403){
                $errorMessage = 'Insufficient rights to view data from this unit';
                return response()->json([
                    'success'   => false,
                    'error'     => $errorMessage,
                ]);                                
            }else if($code == 404){
                $errorMessage = 'Company not found in the Sirene database';
                return response()->json([
                    'success'   => false,
                    'error'     => $errorMessage,
                ]);                                
            }else if($code == 500){
                $errorMessage = 'Internal Server Error';
                return response()->json([
                    'success'   => false,
                    'error'     => $errorMessage,
                ]);                                
            }
        } catch (ClientException $e) {
            return response()->json([
                'success'   => false,
                'error'     => $e->getResponse()->getReasonPhrase(),
            ]);
        }
    }
    /**
     * add a customer
     */
    public function storeCustomer(Request $request){
        $validator = Validator::make($request->all(), [
            'customerStatus'    => 'required',
            'email'             => $request->email != '' ? 'email' : '',
            'siret'             => $request->siret != '' ? 'required|unique:customers,siret,'.$request->id: "",
            'customerTax'       => 'required',
            'numtva'            => $request->numtva != '' ? 'unique:customers,numtva' : '',
        ]);
 
        if ($validator->fails()) {
            return response()->json(['errors'=> $validator->errors(), 'success'=> false]);
        }else{
            $customer = [
                'affiliate_id'          => auth()->user()->affiliate_id,
                'taxe_id'               => $request->customerTax,
                'customer_statut_id'    => $request->customerStatus,
                'customer_categories_id'=> $request->customerCat,
                'customer_paiement_id'  => $request->customerPaiement,
                'customer_pente_id'     => 0,
                'customer_origin_id'    => $request->customerOrigin,
                'customer_materiau_id'  => 0,
                'numtva'                => $request->numtva,
                'naf'                   => $request->naf,
                'siret'                 => $request->siret,
                'email'                 => $request->email,
                'telephone'             => $request->phoneNumber != ''? $request->phoneCountryCode.'|'.$request->phoneNumber : '',
                'company'               => $request->company,
                'raisonsociale'         => $request->raisonsociale,
                'raisonsociale2'        => $request->raisonsociale2,
                'firstname'             => $request->firstName,
                'gender'                => $request->gender,
                'name'                  => $request->lastName,
                
                'libelleadresse'        => $request->address1,
                'libellespecifique'     => $request->address2,
                'centredistributeur'    => $request->address3,
                'codepostal'            => $request->postCode,

                'linkedin'              => $request->linkedin,
                'siteweb'               => $request->website,
                'litige'                => $request->litige,
                'descision'             => $request->descision,
                'active'                => $request->active,
                'master_id'             => $request->masterId,
                'zpe'                   => $request->zpe,
                'environnement'         => $request->environment,
                'statutetablissement'   => $request->statusEtablissement,
                'trancheca'             => $request->trancheCA,
                'trancheeffectif'       => $request->trancheEffectif,
                'tranchetaillecommune'  => $request->trancheCommune,
                'num_client_gx'         => $request->numLCDT,
                'datecreationetablissement'=> Carbon::parse($request->dateCreated)->format('m/d/Y'),
                'signupdate'            => now()->format('Y-m-d'),
                'created_at'            => now(),
                'updated_at'            => now(),
                
            ];
            $customerID = DB::table('customers')->insertGetId($customer);
            foreach ($request->addresses as $address) {
                $newAddress = [
                    'address_type_id'       => $address['addressType'],
                    'country_id'            => 1, // france
                    'customer_id'           => $customerID,
                    'company'               => $customer['company'],
                    'lastname'              => $address['nom'],
                    'firstname'             => $address['firstName'],
                    'gender'                => $customer['gender'],
                    'address1'              => $address['address1'],
                    'address2'              => $address['address2'],
                    'address3'              => $address['address3'],
                    'postcode'              => $address['postCode'],
                    'city'                  => $address['city'],
                    'phone'                 => $address['phoneNumber'] != ''? $address['phoneCode'].'|'.$address['phoneNumber'] : '',
                    'latitude'              => $address['latitude'],
                    'longitude'             => $address['longitude'],
                    'pente'                 => $address['pente'],
                    'surfacetoiture'        => $address['surfacetoiture'],
                    'materiau'              => $address['materiau'],
                    'presenceamiante'       => $address['presenceamiante'],
                    'presenceepc'           => $address['presenceepc'],
                    'accesexterieur'        => $address['accesexterieur'],
                    'presenceapportlumiere' => $address['presenceapportlumiere'],
                    'etattoiture'           => $address['etattoiture'],
                    'accesinterieur'        => $address['accesinterieur'],
                    'hauteurbatiment'       => $address['hauteurbatiment'],
                    'typebatiment'          => $address['typebatiment'],
                    'comment'               => $address['infoNote'],
                    'created_at'            => now(),
                    'updated_at'            => now(),
                ];
                $addressID = DB::table('addresses')->insertGetId($newAddress);
            }

            foreach ($request->contacts as $contact) {
                if( $contact['firstName'] != '' && $contact['name'] != '' && $contact['type'] != '' && $contact['name']){
                    $contactValidator = Validator::make($contact, [
                        'type'              => 'required',
                        'email'             => 'required|unique:contacts,email',
                        'firstName'         => 'required',
                        'name'              => 'required'
                    ]);
                    if(!$contactValidator->fails()){
                        $newContact = [
                            'contact_type_id'       => $contact['type'],
                            'contact_qualite_id'    => $contact['qualite'],
                            'customer_id'           => $customerID,
                            'actif'                 => $contact['actif'],
                            'address_id'            => 0,
                            'num_contact_gx'        => $contact['numGx'],
                            'name'                  => $contact['name'],
                            'firstname'             => $contact['firstName'],
                            'profillinedin'         => $contact['profilLinedin'],
                            'gender'                => $contact['gender'],
                            'email'                 => $contact['email'],
                            'mobile'                => $contact['phoneNumber1'] != '' ? $contact['phoneCountryCode1'].'|'.$contact['phoneNumber1'] : '',
                            'telephone'             => $contact['phoneNumber2'] != '' ? $contact['phoneCountryCode2'].'|'.$contact['phoneNumber2'] : '',
                            'type'                  => DB::table('contact_type')->find($contact['type'])->name,
                            'comment'               => $contact['note'],
                            'acceptsms'             => $contact['acceptSMS'],
                            'acceptmarketing'       => $contact['acceptmarketing'],
                            'acceptcourrier'        => $contact['acceptcourrier'],
                            'created_at'            => now(),
                            'updated_at'            => now(),
                        ];
                        DB::table('contacts')->insert($newContact);
                    }
                }
            }
        }
        return response()->json(['success'=> true, 'id'=> $customerID]);
    }

    /**
     * Get a customer by ID
     */
    public function getCustomer($id){
        $customer = DB::table('customers')
                    ->where('id', $id)
                    ->select(
                        'id', 'raisonsociale', 'raisonsociale2', 'siret', 
                        'num_client_gx as numLCDT', 'company', 'customer_origin_id as customerOrigin', 
                        'customer_categories_id as customerCat', 'customer_paiement_id as customerPaiement', 
                        'taxe_id as customerTax', 'customer_statut_id as customerStatus', 'naf', 'email', 
                        'telephone', 'firstname as firstName', 'gender', 'name as lastName', 'active',
                        'libelleadresse as address1', 'libellespecifique as address2', 'descision', 'master_id as masterId',
                        'centredistributeur as address3', 'codepostal as postCode', 'linkedin', 
                        'siteweb as website', 'litige', 'zpe', 'environnement as environment', 
                        'statutetablissement as statusEtablissement', 'trancheca as trancheCA', 'numtva', 
                        'trancheeffectif as trancheEffectif', 'tranchetaillecommune as trancheCommune', 
                        'datecreationetablissement as dateCreated', 
                        DB::raw('DATE_FORMAT(created_at, "%m/%d/%Y") as created_at'), 
                        DB::raw('DATE_FORMAT(updated_at, "%m/%d/%Y") as updated_at')
                    )
                    ->first();
        $customer->addresses = DB::table('addresses')
                                ->where('customer_id', $id)
                                ->whereNull('deleted_at')
                                ->select(
                                    'id', 'address_type_id as addressType', 'lastname as nom', 
                                    'firstname as firstName', 'address1', 'address2', 'address3', 
                                    'postcode as postCode', 'city', 'latitude', 'longitude', 'pente', 
                                    'surfacetoiture', 'materiau', 'presenceamiante', 'presenceepc', 
                                    'accesexterieur', 'presenceapportlumiere', 'etattoiture', 'accesinterieur', 
                                    'hauteurbatiment', 'typebatiment', 'comment as infoNote', 'phone as phoneNumber'
                                )->get();
        $customer->contacts = DB::table('contacts')
                                ->where('customer_id', $id)
                                ->whereNull('deleted_at')
                                ->select(
                                    'id', 'contact_type_id as type', 'contact_qualite_id as qualite', 'actif', 
                                    'num_contact_gx as numGx', 'name', 'firstname as firstName', 'profillinedin as profilLinedin', 
                                    'gender', 'email', 'mobile', 'telephone', 'comment as note', 
                                    'acceptsms as acceptSMS', 'acceptmarketing', 'acceptcourrier'
                                )
                                ->get();
        return response()->json([
            'customer'  => $customer,
            'customerOrigins'   => DB::table('customer_origins')->select('id as value', 'name as display')->orderBy('name')->get(),
            'status'            => DB::table('customer_statut')->select('id as value', 'name as display')->orderBy('name')->get(),
            'customerCats'      => DB::table('customer_categories')->select('id as value', 'name as display')->orderBy('name')->get(),
            'customerPentes'    => DB::table('customer_pente')->select('id as value', 'name as display')->orderBy('name')->get(),
            'nafs'              => DB::table('customer_naf')->select('code', 'name', 'selection')->orderBy('name')->get(),
            'taxes'             => DB::table('taxes')->select('id as value', 'name as display')->orderBy('id')->get(),
            'addressTypes'      => DB::table('address_type')->select('id as value', 'name as display')->orderBy('name')->get(),
            'customerQualites'  => DB::table('customer_qualite')->select('id as value', 'name as display')->orderBy('id')->get(),
            'customerTypeBatiments' => DB::table('customer_typebatiment')->select('id as value', 'name as display')->orderBy('id')->get(),
            'customerMateriaus' => DB::table('customer_materiau')->select('name as value', 'name as display')->orderBy('id')->get(),            
            'customerPaiements' => DB::table('customer_paiement')->select('id as value', 'name as display')->orderBy('id')->get(),            
            'contactTypes'      => DB::table('contact_type')->select('id as value', 'name as display')->orderBy('id')->get(),
            'contactQualites'   => DB::table('contact_qualite')->select('id as value', 'name as display')->orderBy('id')->get(),
            'customerAddresses' => DB::table('addresses')
                                    ->select('id as value', DB::raw('CONCAT(address1, ", ", CONCAT(postcode, ", ", city)) as display'))
                                    ->where('customer_id', $id)
                                    ->orderBy('id')->get(),
        ]);
    }

    /**
     * Update a customer
     */
    public function updateCustomer(Request $request){
        $validator = Validator::make($request->all(), [
            'customerStatus'    => 'required',
            'siret'             => $request->siret != '' ? 'required|unique:customers,siret,'.$request->id: "",
            'email'             => $request->email != '' ? 'email' : '',
            'customerTax'       => 'required',
            'numtva'            => $request->numtva != '' ? 'unique:customers,numtva,'.$request->id : '',
        ]);
        if($validator->fails()){
            return response()->json([
                'success'   => false,
                'errors'    => $validator->errors()
            ]);
        }else{
            $customerData = [
                'affiliate_id'          => auth()->user()->affiliate_id,
                'taxe_id'               => $request->customerTax,
                'customer_statut_id'    => $request->customerStatus,
                'customer_categories_id'=> $request->customerCat,
                'customer_paiement_id'  => $request->customerPaiement,
                'customer_origin_id'    => $request->customerOrigin,
                'naf'                   => $request->naf,
                'siret'                 => $request->siret,
                'numtva'                => $request->numtva,
                'email'                 => $request->email,
                'telephone'             => $request->phoneNumber != ''? $request->phoneCountryCode.'|'.$request->phoneNumber : '',
                'company'               => $request->company,
                'raisonsociale'         => $request->raisonsociale,
                'raisonsociale2'        => $request->raisonsociale2,
                'firstname'             => $request->firstName,
                'gender'                => $request->gender,
                'name'                  => $request->lastName,
                'libelleadresse'        => $request->address1,
                'libellespecifique'     => $request->address2,
                'centredistributeur'    => $request->address3,
                'codepostal'            => $request->postCode,
                'linkedin'              => $request->linkedin,
                'siteweb'               => $request->website,
                'litige'                => $request->litige,
                'descision'             => $request->descision,
                'active'                => $request->active,
                'master_id'             => $request->masterId,
                'zpe'                   => $request->zpe,
                'environnement'         => $request->environment,
                'statutetablissement'   => $request->statusEtablissement,
                'trancheca'             => $request->trancheCA,
                'trancheeffectif'       => $request->trancheEffectif,
                'tranchetaillecommune'  => $request->trancheCommune,
                'num_client_gx'         => $request->numLCDT,
                'datecreationetablissement'=> Carbon::parse($request->dateCreated)->format('m/d/Y'),
            ];
            DB::table('customers')->where('id', $request->id)->update($customerData);
            DB::table('addresses')->where('customer_id', $request->id)->update([
                'deleted_at'=> now()
            ]);
            foreach ($request->addresses as $address) {
                $addressData = [
                    'address_type_id'       => $address['addressType'],
                    'customer_id'           => $request->id,
                    'company'               => $customerData['company'],
                    'lastname'              => $address['nom'],
                    'firstname'             => $address['firstName'],
                    'gender'                => $customerData['gender'],
                    'address1'              => $address['address1'],
                    'address2'              => $address['address2'],
                    'address3'              => $address['address3'],
                    'postcode'              => $address['postCode'],
                    'city'                  => $address['city'],
                    'phone'                 => $address['phoneNumber'] != ''? $address['phoneCode'].'|'.$address['phoneNumber'] : '',
                    'latitude'              => $address['latitude'],
                    'longitude'             => $address['longitude'],
                    'pente'                 => $address['pente'],
                    'surfacetoiture'        => $address['surfacetoiture'],
                    'materiau'              => $address['materiau'],
                    'presenceamiante'       => $address['presenceamiante'],
                    'presenceepc'           => $address['presenceepc'],
                    'accesexterieur'        => $address['accesexterieur'],
                    'presenceapportlumiere' => $address['presenceapportlumiere'],
                    'etattoiture'           => $address['etattoiture'],
                    'accesinterieur'        => $address['accesinterieur'],
                    'hauteurbatiment'       => $address['hauteurbatiment'],
                    'typebatiment'          => $address['typebatiment'],
                    'comment'               => $address['infoNote'],
                    'deleted_at'            => null,
                ];
                if($address['id'] == ''){
                    $addressData['country_id'] = 1;
                    $addressData['created_at'] = now();
                    $addressData['updated_at'] = now();
                    $addressID = DB::table('addresses')->insertGetId($addressData);
                }else{
                    DB::table('addresses')->where('id', $address['id'])->update($addressData);
                }
            }
    
            DB::table('contacts')->where('customer_id', $request->id)->update([
                'deleted_at'=> now()
            ]);
            foreach ($request->contacts as $contact) {
                $contactData = [
                    'contact_type_id'       => $contact['type'],
                    'contact_qualite_id'    => $contact['qualite'],
                    'customer_id'           => $request->id,
                    'actif'                 => $contact['actif'],
                    'num_contact_gx'        => $contact['numGx'],
                    'name'                  => $contact['name'],
                    'firstname'             => $contact['firstName'],
                    'profillinedin'         => $contact['profilLinedin'],
                    'gender'                => $contact['gender'],
                    'email'                 => $contact['email'],
                    'mobile'                => $contact['phoneCountryCode1'].'|'.$contact['phoneNumber1'],
                    'telephone'             => $contact['phoneCountryCode2'].'|'.$contact['phoneNumber2'],
                    'type'                  => DB::table('contact_type')->find($contact['type'])->name,
                    'comment'               => $contact['note'],
                    'acceptsms'             => $contact['acceptSMS'],
                    'acceptmarketing'       => $contact['acceptmarketing'],
                    'acceptcourrier'        => $contact['acceptcourrier'],
                    'deleted_at'            => null,
                ];
                if($contact['id'] == '' && $contact['name'] != '' && $contact['firstName'] !='' && $contact['type'] != '' && $contact['email']){
                    $contactData['address_id'] = 0;
                    $contactData['created_at'] = now();
                    $contactData['updated_at'] = now();
                    DB::table('contacts')->insert($contactData);
                }else{
                    DB::table('contacts')->where('id', $contact['id'])->update($contactData);
                }
        }
        }
        return response()->json(['success' => true]);
    }
    /**
     * search customers
     */
    public function searchCustomer(Request $request)
    {
        $query = DB::table('customers')
                    ->leftJoin('group', 'customers.group_id', '=', 'group.id')
                    ->leftJoin('taxes', 'customers.taxe_id', '=', 'taxes.id')
                    ->join('contacts', 'customers.id', '=', 'contacts.customer_id')
                    ->select( 
                        'customers.company', 
                        'customers.raisonsociale', 
                        'customers.raisonsociale2', 
                        'group.Name as group',
                        DB::raw('CONCAT(contacts.firstname, " ", contacts.name) as contact'),
                        'customers.telephone', 'contacts.email', 'customers.naf', 'customers.siret',
                        DB::raw('taxes.taux * 100 as tax'), 'customers.id', 'taxes.id as taxId',
                        'contacts.id as contact_id'
                    )
                    ->whereNull('contacts.deleted_at')
                    ->where('customers.raisonsociale', 'like', '%'.$request->search.'%')
                    ->orWhere('customers.raisonsociale2', 'like', '%'.$request->search.'%')
                    ->orWhere('customers.company', 'like', '%'.$request->search.'%');

        return response()->json([
            'data'  => $request->has('showmore') ? $query->get() : $query->take(5)->get(),
            'total' => $query->count()
        ]);
    }

    /**
     * search customers by raisonsocile
     */
    public function searchMaster(Request $request){
        $query = DB::table('customers')
                    ->leftJoin('group', 'customers.group_id', '=', 'group.id')
                    ->leftJoin('taxes', 'customers.taxe_id', '=', 'taxes.id')
                    ->select( 'customers.company', 'customers.raisonsociale', 'group.Name as group',
                        DB::raw('CONCAT(customers.firstname, " ", customers.name) as contact'),
                        'customers.telephone', 'customers.email', 'customers.naf', 'customers.siret',
                        DB::raw('taxes.taux * 100 as tax'), 'customers.id', 'taxes.id as taxId'
                    )->where('customers.raisonsociale', 'like', '%'.$request->search.'%');

        return response()->json([
            'data'  => $request->has('showmore') ? $query->get() : $query->take(5)->get(),
            'total' => $query->count()
        ]);
    }

    /**
     * get customer addresses
     */
    public function getCustomerAddresses(Request $request){
        $addresses = DB::table('addresses')->join('address_type', 'addresses.address_type_id', '=', 'address_type.id')
                    ->select( 
                        'addresses.id', DB::raw('CONCAT(addresses.firstname, " ", addresses.lastname) as name'), 
                        'addresses.address1', 'addresses.address2',
                        'addresses.postcode', 'addresses.city', 'address_type.name as addressType', 
                        'addresses.address1', 'latitude as lat', 'longitude as lon',
                        'addresses.firstname', 'addresses.lastname as nom'
                    )
                    ->where('customer_id', $request->customer_id)
                    ->get();
        return response()->json($addresses);
    }

    /**
     * get customer contacts
     *  @param customerId
     */
    public function getCustomerContacts(Request $request){
        $contacts = DB::table('contacts')->join('contact_qualite', 'contacts.contact_qualite_id', '=', 'contact_qualite.id')
                    ->select( 
                        DB::raw('CONCAT(contacts.firstname, " ", contacts.name) as name'), 'contacts.firstname',
                        'contact_qualite.name as qualite', 'contacts.mobile', 'contacts.email', 'contacts.name as nom',
                        'contacts.comment', 'contacts.id'
                    )
                    ->whereNull('contacts.deleted_at')
                    ->where('contacts.customer_id', $request->customerId)
                    ->get();
        return response()->json($contacts);
    }

    /**
     * Add customer address
     */
    public function addCustomerAddress(Request $request){
        $newAddress = [
            'address_type_id'       => $request->addressType,
            'country_id'            => 1, // france
            'customer_id'           => $request->customerID,
            'lastname'              => $request->lastName,
            'firstname'             => $request->firstName,
            'address1'              => $request->address1,
            'address2'              => $request->address2,
            'address3'              => $request->address3,
            'postcode'              => $request->postCode,
            'city'                  => $request->city,
            'latitude'              => $request->latitude,
            'longitude'             => $request->longitude,
            'created_at'            => now(),
            'updated_at'            => now(),
        ];
        $addressID = DB::table('addresses')->insertGetId($newAddress);
        return response()->json([
            'id' => $addressID,
            'addressType' => DB::table('address_type')->find($request->addressType)->name,
        ]);
    }

    /**
     * Check if email exists or not
     *  @param { table, email }
     * 
     */
    public function checkEmailExists(Request $request){
        if($request->id != 0){
            $validator = Validator::make($request->all(), [
                'email' => 'required|email|unique:'.$request->table,
            ]);
        }else{
            $validator = Validator::make($request->all(), [
                'email' => 'required|email|unique:'.$request->table.','.$request->id,
            ]);
        }
 
        if ($validator->fails()) {
            return response()->json( ['success'=> false, 'errors'=> $validator->errors()] );
        }else{
            return response()->json( ['success'=> true ] );
        }        
    }

}
