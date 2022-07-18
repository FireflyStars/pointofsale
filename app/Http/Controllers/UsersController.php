<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\UsersResource;
use App\Http\Controllers\TableFiltersController;
use App\Models\UserDocument;

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
            $user_documents[] = $document;
        }

        return response()->json($user_documents); 

    }


    public function remove_user_document(UserDocument $document)
    {

        $document->delete();

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

        $document = new UserDocument;
        
        $document->user_id = $user->id;
        $document->human_readable_filename = $file->getClientOriginalName();
        $document->name = $request->name;
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


}
