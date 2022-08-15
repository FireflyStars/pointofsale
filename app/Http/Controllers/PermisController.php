<?php

namespace App\Http\Controllers;

use App\Models\UserDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PermisController extends Controller
{
    
    public function index(Request $request) 
    {

        $permis = UserDocument::query();

        $permis = $permis->select(
            'user_documents.id',
            'user_documents.user_id',
            'user_doc_permis.name',
            'user_documents.comment',
            'user_status.name as status',
            DB::raw('IFNULL(orders.montant, 0) as montant'),
            DB::raw('IFNULL(orders.count_orders, 0) as count_orders'),
            DB::raw(
                'TRIM(
                    CONCAT(
                        permis.contactname,  
                        "<br>",
                        permis.phone
                    )
                ) 
            as contact'),
            DB::raw('DATE_FORMAT(permis.created_at, "%Y-%m-%d") as created_at'),            
        );

        $permis = $permis->leftJoin('supplier_type', 'supplier_type.id', '=', 'supplier_type_id');

        $data = (new TableFiltersController)->sorts($request, $permis, 'permis.id');
        $data = (new TableFiltersController)->filters($request, $data);

        $data = $data->take($request->take ?? 15)
        ->skip($request->skip ?? 0);

        return $data;

    }

}
