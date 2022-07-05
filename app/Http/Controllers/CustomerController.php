<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

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
            'contactTypes'      => DB::table('contact_type')->select('id as value', 'name as display')->orderBy('id')->get(),
            'contactQualites'   => DB::table('contact_qualite')->select('id as value', 'name as display')->orderBy('id')->get(),            
        ]);
    }

    /**
     * add a customer
     */
    public function storeCustomer(Request $request){
        $validator = Validator::make($request->all(), [
            'naf'               => 'required',
            'customerStatus'    => 'required',
            'email'             => $request->email != '' ? 'email' : '',
            'customerTax'       => 'required'
        ]);
 
        if ($validator->fails()) {
            return response()->json(['errors'=> $validator->errors(), 'success'=> false]);
        }else{
            $customer = [
                'affiliate_id'          => auth()->user()->affiliate_id,
                'taxe_id'               => $request->customerTax,
                'customer_statut_id'    => $request->customerStatus,
                'customer_categories_id'=> $request->customerCat,
                'customer_pente_id'     => 0,
                'customer_origin_id'    => $request->customerOrigin,
                'customer_materiau_id'  => 0,
                'naf'                   => $request->naf,
                'siret'                 => $request->siret,
                'email'                 => $request->email,
                'telephone'             => $request->phoneCountryCode.'|'.$request->phoneNumber,
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
                'active'                => 1,
                'zpe'                   => $request->zpe,
                'environnement'         => $request->environment,
                'statutetablissement'   => $request->statusEtablissement,
                'trancheca'             => $request->trancheCA,
                'trancheeffectif'       => $request->trancheEffectif,
                'tranchetaillecommune'  => $request->trancheCommune,
                'num_client_gx'         => $request->numLCDT,
                'datecreationetablissement'=> $request->dateCreated,
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
                    'mobile'                => $contact['phoneCountryCode1'].'|'.$contact['phoneNumber1'],
                    'telephone'             => $contact['phoneCountryCode2'].'|'.$contact['phoneNumber2'],
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
        return response()->json(['success'=> true]);
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
                        'customer_categories_id as customerCat', 'taxe_id as customerTax', 
                        'customer_statut_id as customerStatus', 'naf', 'email', 
                        'telephone', 'firstname as firstName', 'gender', 'name as lastName', 
                        'libelleadresse as address1', 'libellespecifique as address2', 
                        'centredistributeur as address3', 'codepostal as postCode', 'linkedin', 
                        'siteweb as website', 'litige', 'zpe', 'environnement as environment', 
                        'statutetablissement as statusEtablissement', 'trancheca as trancheCA', 
                        'trancheeffectif as trancheEffectif', 'tranchetaillecommune as trancheCommune', 
                        'datecreationetablissement as dateCreated', 
                        DB::raw('DATE_FORMAT(created_at, "%m/%d/%Y") as created_at'), 
                        DB::raw('DATE_FORMAT(updated_at, "%m/%d/%Y") as updated_at')
                    )
                    ->first();
        $customer->addresses = DB::table('addresses')
                                ->where('customer_id', $id)
                                ->select(
                                    'id', 'address_type_id as addressType', 'lastname as nom', 
                                    'firstname as firstName', 'address1', 'address2', 'address3', 
                                    'postcode as postCode', 'city', 'latitude', 'longitude', 'pente', 
                                    'surfacetoiture', 'materiau', 'presenceamiante', 'presenceepc', 
                                    'accesexterieur', 'presenceapportlumiere', 'etattoiture', 'accesinterieur', 
                                    'hauteurbatiment', 'typebatiment', 'comment as infoNote'
                                )->get();
        $customer->contacts = DB::table('contacts')
                                ->where('customer_id', $id)
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
            'contactTypes'      => DB::table('contact_type')->select('id as value', 'name as display')->orderBy('id')->get(),
            'contactQualites'   => DB::table('contact_qualite')->select('id as value', 'name as display')->orderBy('id')->get(),
        ]);
    }

    /**
     * Update a customer
     */
    public function updateCustomer(Request $request){
        $customerData = [
            'affiliate_id'          => auth()->user()->affiliate_id,
            'taxe_id'               => $request->customerTax,
            'customer_statut_id'    => $request->customerStatus,
            'customer_categories_id'=> $request->customerCat,
            'customer_origin_id'    => $request->customerOrigin,
            'naf'                   => $request->naf,
            'siret'                 => $request->siret,
            'email'                 => $request->email,
            'telephone'             => $request->phoneCountryCode.'|'.$request->phoneNumber,
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
            'zpe'                   => $request->zpe,
            'environnement'         => $request->environment,
            'statutetablissement'   => $request->statusEtablissement,
            'trancheca'             => $request->trancheCA,
            'trancheeffectif'       => $request->trancheEffectif,
            'tranchetaillecommune'  => $request->trancheCommune,
            'num_client_gx'         => $request->numLCDT,
            'datecreationetablissement'=> $request->dateCreated,
        ];
        DB::table('customers')->where('id', $request->id)->update($customerData);
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
            ];
            if($contact['id'] == ''){
                $contactData['address_id'] = 0;
                $contactData['created_at'] = now();
                $contactData['updated_at'] = now();
                DB::table('contacts')->insert($contactData);
            }else{
                DB::table('contacts')->where('id', $contact['id'])->update($contactData);
            }
        }
        return response()->json(['success' => true]);
    }
    /**
     * search customers
     */
    public function searchCustomer(Request $request){
        $query = DB::table('customers')
                    ->leftJoin('group', 'customers.group_id', '=', 'group.id')
                    ->leftJoin('taxes', 'customers.taxe_id', '=', 'taxes.id')
                    ->select( 'customers.company', 'customers.raisonsociale', 'group.Name as group',
                        DB::raw('CONCAT(customers.firstname, " ", customers.name) as contact'),
                        'customers.telephone', 'customers.email', 'customers.naf', 'customers.siret',
                        DB::raw('taxes.taux * 100 as tax'), 'customers.id', 'taxes.id as taxId'
                    )->where('customers.firstname', 'like', '%'.$request->search.'%')->orWhere('customers.name', 'like', '%'.$request->search.'%');

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
                        DB::raw('CONCAT(contacts.firstname, " ", contacts.name) as name'),
                        'contact_qualite.name as qualite', 'contacts.mobile', 'contacts.email',
                        'contacts.comment', 'contacts.id'
                    )
                    ->where('customer_id', $request->customerId)
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
     * Add customer contact
     */
    public function addCustomerContact(Request $request){
        $contactData = [
            'contact_type_id'       => $request->type == '' ? 1 : $request->type,
            'contact_qualite_id'    => $request->qualite == '' ? 1 : $request->qualite,
            'customer_id'           => $request->customerID,
            'actif'                 => $request->actif,
            'num_contact_gx'        => $request->numGx,
            'name'                  => $request->name,
            'firstname'             => $request->firstName,
            'address_id'            => $request->address == '' ? 0 : $request->address,
            'profillinedin'         => $request->profilLinedin,
            'gender'                => $request->gender,
            'email'                 => $request->email,
            'mobile'                => $request->phoneCountryCode1.'|'.$request->phoneNumber1,
            'telephone'             => $request->phoneCountryCode2.'|'.$request->phoneNumber2,
            'type'                  => DB::table('contact_type')->find($request->type)->name,
            'comment'               => $request->note,
            'acceptsms'             => $request->acceptSMS,
            'acceptmarketing'       => $request->acceptmarketing,
            'acceptcourrier'        => $request->acceptcourrier,
        ];        
        $contactId = DB::table('contacts')->insertGetId($contactData);
        return response()->json([
            'id' => $contactId
        ]);
    }

    /**
     * Check if email exists or not
     *  @param { table, email }
     * 
     */
    public function checkEmailExists(Request $request){
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:'.$request->table,
        ]);
 
        if ($validator->fails()) {
            return response()->json( ['success'=> false, 'errors'=> $validator->errors()] );
        }else{
            return response()->json( ['success'=> true ] );
        }        
    }

}
