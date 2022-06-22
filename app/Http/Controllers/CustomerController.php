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
            'taxes'              => DB::table('taxes')->select('id as value', 'name as display')->orderBy('name')->get(),
            'addressTypes'      => DB::table('address_type')->select('id as value', 'name as display')->orderBy('name')->get(),
            'contactTypes'      => DB::table('contact_type')->select('id as value', 'name as display')->orderBy('id')->get(),
            'customerQualites'  => DB::table('customer_qualite')->select('id as value', 'name as display')->orderBy('id')->get(),
            'customerTypeBatiments' => DB::table('customer_typebatiment')->select('id as value', 'name as display')->orderBy('id')->get(),
            'customerMateriaus'  => DB::table('customer_materiau')->select('name as value', 'name as display')->orderBy('id')->get(),
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
                'name'                  => $request->firstName.' '.$request->lastName,
                
                'libelleadresse'        => $request->address1,
                'libelleadresse'        => $request->address2,
                'centredistributeur'    => $request->address3,
                'codepostal'            => $request->postCode,

                'linkedin'              => $request->linkedin,
                'siteweb'               => $request->website,
                'litige'                => $request->litige,
                'active'                => $request->actif,
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
                    'profilLinedin'         => $contact['profilLinedin'],
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
     * Adding a new customer
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
                        DB::raw('CONCAT(addresses.firstname, " ", addresses.lastname) as name'), 'addresses.address1', 'addresses.address2',
                        'addresses.postcode', 'addresses.city', 'address_type.name as addressType', 'addresses.id', 'latitude as lat', 'longitude as lon'
                    )
                    ->where('customer_id', $request->customer_id)
                    ->get();
        return response()->json($addresses);
    }

    /**
     * Add customer address
     */
    public function addCustomerAddress(Request $request){
        $newAddress = [
            'address_type_id'       => $request->addressType,
            'country_id'            => 1, // france
            'customer_id'           => $request->customerID, // france
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
}
