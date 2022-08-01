<?php

namespace App\Http\Controllers;

use App\Models\Paiement;
use Illuminate\Http\Request;
use App\Models\PaiementState;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\TableFiltersController;
use App\Http\Resources\PaiementResource;

class PaiementsController extends Controller
{

    private function get_data(Request $request) 
    {

        $paiements = Paiement::query();

        $paiements->select(
            'paiements.id',
            'invoice_id',
            'paiements.affiliate_id',
            'customers.name as customer_name',
            'users.name as responsable',
            'paiement_state_id',
            'montantpaiement',
            DB::raw('DATE_FORMAT(datepaiement, "%Y-%m-%d") as datepaiement'),
        );

        $paiements = $paiements->leftJoin('users', 'users.id', '=', 'paiements.responsable_id')
        ->leftJoin('customers', 'customers.id', '=', 'paiements.customer_id')
        ->leftJoin('paiement_states', 'paiement_states.id', '=', 'paiements.paiement_state_id');

        $data = (new TableFiltersController)->sorts($request, $paiements, 'paiements.id');
        $data = (new TableFiltersController)->filters($request, $data);

        $data = $data->take($request->take ?? 15)
        ->skip($request->skip ?? 0);

        return $data;   

    }

    public function index(Request $request) 
    {

        return response()->json(
            $this->get_data($request)->get()
        );
    
    }

    public function paiements_mes(Request $request) 
    {
        return response()->json(
            $this->get_data($request)
            ->where('paiements.affiliate_id', $request->user()->affiliate_id)
            ->get()
        );
    }

    public function paiements_validar(Request $request) 
    {
        return response()->json(
            $this->get_data($request)
            ->where('paiement_states.name', 'VALIDER')
            ->get()
        );
    }

    

    public function paiement_types_formatted() 
    {

        return response()->json(
            PaiementState::select(
                'id',
                'name as value'
            )->get()
        );

    }

    public function paiement_types() 
    {

        return response()->json(
            PaiementState::all()
        );
        
    }

    public function get_paiement_details(Paiement $paiement) 
    {
        return response()->json(
            new PaiementResource($paiement)
        );
    }

    public function get_history(Paiement $paiement) 
    {
        
        return response()->json(
            $paiement->history()
            ->latest('created_at')
            ->get()
            ->load('state', 'user')
        );

    }

    public function valider_paiement(Paiement $paiement) 
    {
        
        $paiement->update([
            'paiement_state_id' => 2
        ]);

        return response()->noContent();

    }

}
