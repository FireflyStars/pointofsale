<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\UsersResource;
use App\Http\Controllers\TableFiltersController;
use App\Models\UserDocPermis;
use App\Models\UserDocument;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UsersController extends Controller
{

    public function __construct() 
    {
        $this->middleware(['auth']);
    }

    public function index(Request $request) 
    {
        
        $users = User::query();

        $users = $users->select(
            'users.id',
            'users.email as email',
            'users.firstname as prenom',
            'users.name as nom',
            'roles.name as role_name',
            'roles.display_name as role_display_name',
            'affiliates.name as affiliate_name',
            'user_status.name as status_name',
            'user_status.color as status_color',
            'user_type.name as type_name',
            'user_type.color as type_color',
            DB::raw("DATE_FORMAT(users.created_at, '%Y-%m-%d') as created_at"),
        );

        $users = $users->leftJoin('roles', 'roles.id', '=', 'users.role_id')
        ->leftJoin('affiliates', 'affiliates.id', '=', 'users.affiliate_id')
        ->leftJoin('user_status', 'user_status.id', 'users.user_status_id')
        ->leftJoin('user_type', 'user_type.id', 'users.user_type_id');

        $users = (new TableFiltersController)->sorts($request, $users, 'users.id');
        $users = (new TableFiltersController)->filters($request, $users);

        $users = $users->where('users.affiliate_id', $request->user()->affiliate_id)
        ->take($request->take ?? 15)
        ->skip($request->skip ?? 0)
        ->get(); 

        return response()->json($users);

    }

    /**
     * Get user's status, role and type to create it
     * 
     */
    public function getUserInfo(){
        return response()->json([
            'userStatus'    =>  DB::table('user_status')->select('id as value', 'name as display')->get(),
            'userRole'      =>  DB::table('roles')->select('id as value', 'name as display')->get(),
            'userType'      =>  DB::table('user_type')->select('id as value', 'name as display')->get(),
        ]);
    }
    /**
     * create a user
     */
    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'firstName'         => 'required',
            'name'              => 'required',
            'email'             => 'required|unique:users,email',
        ], [
            'firstName.required' => 'Please enter prenom!',
            'name.required' => 'Please enter nom!',
            'email.required' => 'Please enter email!',
            'email.email' => 'It\'s not valid email address!',
            'email.unique' => 'It has already been taken!',
        ]);
        if($validator->fails()){
            return response()->json([
                'success'   => false,
                'errors'    => $validator->errors()
            ]);
        }else{
            $user = new User();
            $user->role_id = $request->roleId;
            $user->user_status_id = $request->statusId;
            $user->user_type_id = $request->typeId;
            $user->name = $request->name;
            $user->firstname = $request->firstName;
            $user->email = $request->email;
            $user->password = Hash::make('123456789');
            $user->coordpersonnelles = $request->coordpersonnelles;
            $user->contacturgence = $request->contacturgence;
            $user->comment = $request->comment;
            $user->portable = $request->portableCode.'|'.$request->portable;
            $user->dateentree = $request->dateentree;
            $user->datesorti = $request->datesorti;
            // $user->settings = '{"locale":"fr"}';
            $user->affiliate_id = auth()->user()->affiliate_id;
            $user->save();

            return response()->json([
                'success'   => true,
                'id'        => $user->id
            ]);        
        }
    }
    /**
     * edit a user
     */
    public function edit(User $user){
        return response()->json([
            'user'  => [
                'id'        => $user->id,
                'name'      => $user->name,
                'firstName' => $user->firstname,
                'email'     => $user->email,
                'gender'    => $user->gender,
                'coordpersonnelles' => $user->coordpersonnelles,
                'contacturgence'    => $user->contacturgence,
                'comment'           => $user->comment,
                'portable'          => $user->portable ? explode('|', $user->portable)[1] : '',
                'portableCode'      => $user->portable ? explode('|', $user->portable)[0] : '+33',
                'datesorti'         => $user->datesorti,
                'dateentree'        => $user->dateentree,
                'statusId'          => $user->user_status_id,
                'typeId'            => $user->user_type_id,
                'roleId'            => $user->role_id,
                'createdAt'         => $user->created_at->format('Y-m-d'),
                'updatedAt'         => $user->updated_at->format('Y-m-d'),
                'affiliateId'       => $user->affiliate_id,
                'userAffiliateId'   => auth()->user()->affiliate_id,
            ],
            'userStatus'    =>  DB::table('user_status')->select('id as value', 'name as display')->get(),
            'userRole'      =>  DB::table('roles')->select('id as value', 'name as display')->get(),
            'userType'      =>  DB::table('user_type')->select('id as value', 'name as display')->get(),
        ]);
    }
    /**
     * edit a user
     */
    public function update(Request $request, User $user){
        $validator = Validator::make($request->all(), [
            'firstName'         => 'required',
            'name'              => 'required',
            'email'             => 'required|email|unique:users,email,'.$user->id,
        ], [
            'firstName.required' => 'Please enter prenom!',
            'name.required' => 'Please enter nom!',
            'email.required' => 'Please enter email!',
            'email.email' => 'It\'s not valid email address!',
            'email.unique' => 'It has already been taken!',
        ]);
        if($validator->fails()){
            return response()->json([
                'success'   => false,
                'errors'    => $validator->errors()
            ]);
        }else{
            $user->role_id = $request->roleId;
            $user->user_status_id = $request->statusId;
            $user->user_type_id = $request->typeId;
            $user->name = $request->name;
            $user->firstname = $request->firstName;
            $user->email = $request->email;
            $user->coordpersonnelles = $request->coordpersonnelles;
            $user->contacturgence = $request->contacturgence;
            $user->comment = $request->comment;
            $user->portable = $request->portableCode.'|'.$request->portable;
            $user->dateentree = $request->dateentree;
            $user->datesorti = $request->datesorti;
            $user->affiliate_id = auth()->user()->affiliate_id;
            $user->save();
            return response()->json([
                'success'   => true,
            ]);        
        }
    }
    public function get_details(User $user) 
    {
        return response()->json(
            new UsersResource($user)
        );
    }

    public function load_user_documents(User $user, Request $request)
    {

        $user_documents = array();

        $documents = $user->documents()
                    ->latest('created_at')
                    ->when($request->has('take') && $request->take != null, function($query) use ($request) {
                        $query->take($request->take ?? 3);
                    })
                    ->get();

        foreach($documents as $document)
        {
            $document->date_expired = Carbon::parse($document->expires)->format('d/m/Y');
            $document->date_document = Carbon::parse($document->dateofdocument)->format('d/m/Y');
            $document->user = $document->user;
            $document->strtotime = strtotime($document->created_at);
            $document->fullpath = rtrim(config('app.url'), '/') . '/storage' . '/' . $document->file_path;
            $document->userPermis = $document->userPermis;
            $user_documents[] = $document;
        }

        return response()->json($user_documents); 

    }

    public function permis_list() 
    {
        
        return response()->json(
            UserDocPermis::select('id as value', 'name as display')
            ->get()
        );

    }


    public function remove_user_document(UserDocument $document)
    {

        $document->delete();

        return response()->noContent();

    }

    public function get_document_url(UserDocument $document)
    {

        return response()->json(
            array('document_url' => route('downloadPdfFile') . '?path=' . $document->file_path . '&filename=' . $document->human_readable_filename)
        );
    
    }

    public function upload_user_document(Request $request)
    {

        $request->validate([
            'files'        => 'required|mimetypes:application/pdf',
            'name'         => 'required',
            'dateExpired'  => 'required',
            'dateDocument' => 'required'
        ]);

        $file = $request->file('files');

        $user = User::find($request->userId);

        $user_permis = UserDocPermis::find($request->name);

        $document = new UserDocument;
        
        $document->user_id = $user->id;
        $document->human_readable_filename = $file->getClientOriginalName();
        $document->user_doc_permi_id = $user_permis->id;
        $document->name = $user_permis->name;
        $document->expires = $request->dateExpired;
        $document->dateofdocument = $request->dateDocument;
        $uuid_filename = DB::select('select UUID() AS uuid')[0]->uuid;
        $filename = $uuid_filename . '.' . $file->getClientOriginalExtension();
        $document->file_path = $file->storeAs('USERS/USER_' . $document->user_id . '/user_documents', $filename, 'public');
        $document->save();
    }

    public function delete_user(User $user) 
    {
        $user->delete();
        return response()->json("User deleted");
    }

    /**
     * 
     * Get permis by users
     */
    public function getPermisByUser(){
        $permis = UserDocPermis::select('id', 'name')->get();
        $users = User::where('user_status_id', 1)->select('id', 'name')->get();
        $data = [];
        foreach ($users as $user) {
            $user_permis = [];
            foreach ($permis as $item) {
                $user_permis[$item->id] = 
                $user_doc = UserDocument::where('user_id', $user->id)->where('user_doc_permi_id', $item->id)->first();
                if($user_doc){
                    $bgColor = '';
                    $color = '';
                    $now = Carbon::now();
                    $exp_date = Carbon::parse($user_doc->expires);
                    $diff = $now->diffInMonths($exp_date);
                    if($diff >= 3 && $exp_date->isAfter($now)){
                        $bgColor = 'rgba(66, 167, 30, 0.2)';
                        $color = '#42A71E';
                    }else if($diff < 3 && $exp_date->isAfter($now)){
                        $bgColor = 'rgba(241, 210, 164, 0.7)';
                        $color = '#000000';
                    }else if( $exp_date->isBefore($now) ){
                        $bgColor = 'rgba(255, 0, 0, 0.7)';
                        $color = '#000000';
                    }                    
                    $user_permis[$item->id] = [
                        'expDate'   =>  Carbon::parse($user_doc->expires)->format('d/m/Y'),
                        'bgColor'   =>  $bgColor,
                        'color'     =>  $color
                    ];
                }else{
                    $user_permis[$item->id] = [
                        'expDate'   =>  '--',
                        'bgColor'   =>  '#E0E0E0',
                        'color'     =>  '#000000'
                    ];
                }
            }
            $data[$user->id] = [
                'name'  => $user->name,
                'permis'=>  $user_permis
            ];
        }
        return response()->json([
            'permis'=>  $permis,
            'matrixData'  =>  $data,
        ]);
    }
}
