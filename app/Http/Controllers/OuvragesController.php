<?php

namespace App\Http\Controllers;

use App\Models\Ouvrage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\TableFiltersController;
use App\Http\Resources\OuvrageResource;
use App\Models\OuvrageDetailAffiliate;
use App\Models\OuvrageTask;
use App\Models\OuvrageTaskAffiliate;

class OuvragesController extends Controller
{

    public function __construct()
    {
        $this->middleware(['auth']);
    }
    
    public function index(Request $request) 
    {

        $ouvrages = Ouvrage::query();

        $ouvrages->select(
            'ouvrages.id',
            'ouvrages.name',
            'textchargeaffaire',
            'codelcdt',
            'units.id as unit_id',
            'ouvrage_prestation.name as ouvrage_prestation_name',
            'ouvrage_toit.name as ouvrage_toit_name',
            'ouvrage_metier.name as ouvrage_metier_name',
            DB::raw('DATE_FORMAT(ouvrages.created_at, "%Y-%m-%d") as created_at')
        )->leftJoin('units', 'units.id', '=', 'ouvrages.unit_id')
        ->leftJoin('ouvrage_prestation', 'ouvrage_prestation.id', '=', 'ouvrage_prestation_id')
        ->leftJoin('ouvrage_toit', 'ouvrage_toit.id', '=', 'ouvrage_toit_id')
        ->leftJoin('ouvrage_metier', 'ouvrage_metier.id', '=', 'ouvrage_metier_id');

        $ouvrages = (new TableFiltersController)->sorts($request, $ouvrages, 'ouvrages.id');
        $ouvrages = (new TableFiltersController)->filters($request, $ouvrages);
        
        $ouvrages = $ouvrages
                ->skip($request->skip ?? 0)
                ->take($request->take ?? 15);

        return response()->json(
            $ouvrages->get()
        );
    }

    public function show(Ouvrage $ouvrage) 
    {
        return response()->json(
            new OuvrageResource($ouvrage)
        );
    }

    public function valider(Request $request, Ouvrage $ouvrage) 
    {
        
        $affiliate = $ouvrage->OuvrageAffiliate;

        if(is_null($affiliate)) 
        {
            $ouvrage->OuvrageAffiliate()->create([
                'affiliate_id' => $request->user()->affiliate_id,
                'textcustomer' => $request->textcustomer
            ]);
        }
        else 
        {
            $ouvrage->OuvrageAffiliate()->update([
                'textcustomer' => $request->textcustomer
            ]);
        }

        foreach($request->tasks as $task) 
        {
            $task_affiliate = $task['ouvrage_affiliate'];
            $ouvrage_task = OuvrageTask::find($task['id']);
            if(is_null($task_affiliate)) 
            {
                $ouvrage_task->OuvrageAffiliate()->create([
                    'affiliate_id' => $request->user()->affiliate_id,
                    'textcustomer' => $task['affiliated_textcustomer']
                ]);
            }
            else 
            {
                $task_affiliate = OuvrageTaskAffiliate::find($task['ouvrage_affiliate']['id']);
                $ouvrage_task->OuvrageAffiliate()->update([
                    'textcustomer' => $task['affiliated_textcustomer']
                ]);
            }


            foreach($task['details'] as $detail) 
            {
                if(is_null($detail['ouvrage_affiliate'])) 
                {
                    $affiliate_detail = new OuvrageDetailAffiliate;
                    $affiliate_detail->affiliate_id = $request->user()->affiliate_id;
                    $affiliate_detail->qty = $detail['ouvrage_detail_qty'];
                    $affiliate_detail->ouvrage_detail_id = $detail['id'];
                    $affiliate_detail->save();
                }
                else 
                {
                    $affiliate_detail = OuvrageDetailAffiliate::find($detail['ouvrage_affiliate']['id']);
                    $affiliate_detail->qty = $detail['ouvrage_detail_qty'];
                    $affiliate_detail->save();
                }
            }


        }


        return response()->json('Ouvrage Validated');

    }

}
