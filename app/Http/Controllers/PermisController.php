<?php

namespace App\Http\Controllers;

use App\Models\UserDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\TableFiltersController;

class PermisController extends Controller
{
    
    public function index(Request $request) 
    {

        $permis = UserDocument::query();

        $permis = $permis->select(
            'user_documents.id',
            'user_documents.user_id',
            'user_doc_permis.name as name', 
            DB::raw('DATE_FORMAT(user_documents.dateofdocument, "%Y-%m-%d") as dateofdocument'),            
            DB::raw('DATE_FORMAT(user_documents.expires, "%Y-%m-%d") as expires'),            
            DB::raw('DATE_FORMAT(user_documents.created_at, "%Y-%m-%d") as created_at'),            
        );

        $permis = $permis->leftJoin('user_doc_permis', 'user_doc_permis.id', '=', 'user_documents.user_doc_permi_id');

        $data = (new TableFiltersController)->sorts($request, $permis, 'user_documents.id');
        $data = (new TableFiltersController)->filters($request, $data);

        $data = $data->take($request->take ?? 15)
        ->skip($request->skip ?? 0)
        ->get();

        return response()->json($data);

    }

}
