<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Contact;

class ContactController extends Controller
{
    /**
     * Create a contact
     */
    public function create(Request $request){
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
     * Get a contact and return by json
     */
    public function getContact($id){
        
    }
    /**
     * Update a contact
     */
    public function update(Contact $contact){
        
    }
    /**
     * delete a contact
     */
    public function destroy(Contact $contact){
        
    }
}
