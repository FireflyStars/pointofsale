<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Models\Contact;
use App\Models\Customer;

class ContactController extends Controller
{
    /**
     * Create a contact
     */
    public function create(Request $request){
        $validator = Validator::make($request->all(), [
            'type'              => 'required',
            'email'             => 'required|unique:contacts,email',
            'firstName'         => 'required',
            'name'              => 'required'
        ]);        
        if($validator->fails()){
            return response()->json([
                'success'   => false,
                'errors'    => $validator->errors()
            ]);
        }else{
            $contactData = [
                'contact_type_id'       => $request->type == '' ? 1 : $request->type,
                'contact_qualite_id'    => $request->qualite == '' ? 1 : $request->qualite,
                'customer_id'           => $request->customer['id'],
                'actif'                 => 1,
                'num_contact_gx'        => $request->numGx,
                'name'                  => $request->name,
                'firstname'             => $request->firstName,
                'address_id'            => $request->address == '' ? 0 : $request->address,
                'profillinedin'         => $request->profilLinedin,
                'gender'                => $request->gender,
                'email'                 => $request->email,
                'mobile'                => $request->phoneNumber1 ? $request->phoneCountryCode1.'|'.$request->phoneNumber1 : '',
                'telephone'             => $request->phoneNumber2 ? $request->phoneCountryCode2.'|'.$request->phoneNumber2: '',
                'type'                  => DB::table('contact_type')->find($request->type)->name,
                'comment'               => $request->note,
                'acceptsms'             => $request->acceptSMS,
                'acceptmarketing'       => $request->acceptmarketing,
                'acceptcourrier'        => $request->acceptcourrier,
            ];
            $contactId = DB::table('contacts')->insertGetId($contactData);
            return response()->json([
                'success'   => true,
                'id'        => $contactId
            ]);        
        }
    }
    /**
     * Get a contact
     */
    public function edit(Contact $contact){
        $customer = $contact->customer_id ? Customer::find($contact->customer_id) : null;
        return response()->json([
            'contact'=>[
                'type'      => $contact->contact_type_id,
                'qualite'   => $contact->contact_qualite_id,
                'active'    => $contact->actif,
                'numGx'     => $contact->num_contact_gx,
                'name'      => $contact->name,
                'firstName' => $contact->firstname,
                'address'   => $contact->address_id,
                'profilLinedin'   => $contact->profillinedin,
                'gender'    => $contact->gender,
                'email'     => $contact->email,
                'phoneCountryCode2' => $contact->mobile,
                'phoneNumber2'      => $contact->mobile,
                'phoneCountryCode1' => $contact->telephone,
                'phoneNumber1'      => $contact->telephone,
                'note'      => $contact->comment,
                'acceptSMS' => $contact->acceptsms,
                'acceptmarketing' => $contact->acceptmarketing,
                'acceptcourrier' => $contact->acceptcourrier,
                'customer'  => [
                    'id'            => $customer ? $contact->customer_id : 0,
                    'company'       => $customer ? $customer->company : '',
                    'raisonsocial'  => $customer ? $customer->raisonsocial : '',
                    'group'         => $customer ? $customer->group->Name : '',
                    'contact'       => $customer ? $customer->firstname.' '.$customer->name : '',
                    'telephone'     => $customer ? $customer->telephone : '',
                    'tax'           => $customer ? $customer->tax->name : '',
                    'naf'           => $customer ? $customer->naf : '',
                    'siret'         => $customer ? $customer->siret : '',
                ]
            ],
            'contactTypes'      => DB::table('contact_type')->select('id as value', 'name as display')->orderBy('id')->get(),
            'contactQualites'   => DB::table('contact_qualite')->select('id as value', 'name as display')->orderBy('id')->get(),
            'customerAddresses' => $customer ? DB::table('addresses')
                                    ->select('id as value', DB::raw('CONCAT(address1, ", ", CONCAT(postcode, ", ", city)) as display'))
                                    ->where('customer_id', $customer->id)
                                    ->orderBy('id')->get() : [],
        ]);
    }
    /**
     * Update a contact
     */
    public function update(Request $request, Contact $contact){
        $validator = Validator::make($request->all(), [
            'type'              => 'required',
            'email'             => 'required|unique:contacts,email,'.$contact->id,
            'firstName'         => 'required',
            'name'              => 'required'
        ]);        
        if($validator->fails()){
            return response()->json([
                'success'   => false,
                'errors'    => $validator->errors()
            ]);
        }else{
            $contactData = [
                'contact_type_id'       => $request->type == '' ? 1 : $request->type,
                'contact_qualite_id'    => $request->qualite == '' ? 1 : $request->qualite,
                'customer_id'           => $request->customer['id'],
                'actif'                 => $request->active,
                'num_contact_gx'        => $request->numGx,
                'name'                  => $request->name,
                'firstname'             => $request->firstName,
                'address_id'            => $request->address == '' ? 0 : $request->address,
                'profillinedin'         => $request->profilLinedin,
                'gender'                => $request->gender,
                'email'                 => $request->email,
                'mobile'                => $request->phoneNumber1 ? $request->phoneCountryCode1.'|'.$request->phoneNumber1 : '',
                'telephone'             => $request->phoneNumber2 ? $request->phoneCountryCode2.'|'.$request->phoneNumber2: '',
                'type'                  => DB::table('contact_type')->find($request->type)->name,
                'comment'               => $request->note,
                'acceptsms'             => $request->acceptSMS,
                'acceptmarketing'       => $request->acceptmarketing,
                'acceptcourrier'        => $request->acceptcourrier,
            ];
            $contact->update($contactData);
            return response()->json([
                'success'   => true,
            ]);
        }        
    }
    /**
     * delete a contact
     */
    public function destroy(Contact $contact){
        
    }
}
