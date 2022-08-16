<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Event;

use Carbon\Carbon;

class HomeController extends Controller
{
    public function index(){
        $this_month = [Carbon::now()->startOfMonth()->startOfDay()->toDateTimeString(), Carbon::now()->endOfDay()->toDateTimeString()];
        $last_month = [Carbon::now()->subMonth(1)->startOfMonth()->startOfDay()->toDateTimeString(), Carbon::now()->subMonth(1)->startOfMonth()->endOfDay()->toDateTimeString()];
        $devisAFaire = DB::table('orders')
                        ->whereIn('order_state_id', [ 1, 2, 6, 15 ])
                        ->select(DB::raw('COUNT(*) as count'))
                        ->whereBetween('datecommande', $this_month)
                        ->value('count') ?? 0;
        $devisAttenteClient = DB::table('orders')
                        ->whereIn('order_state_id', [ 3 ])
                        ->whereBetween('datecommande', $this_month)
                        ->select(DB::raw('COUNT(*) as count'))
                        ->value('count') ?? 0;
        $paiement = DB::table('invoices')
                        ->whereIn('invoice_state_id', [ 1, 2, 5, 6 ])
                        ->whereBetween('dateecheance', $this_month)
                        ->select(DB::raw('COUNT(*) as count'))
                        ->value('count') ?? 0;
        $paiementToCompare = DB::table('invoices')
                        ->whereIn('invoice_state_id', [ 3 ])
                        ->whereBetween('dateecheance', $this_month)
                        ->select(DB::raw('COUNT(*) as count'))
                        ->value('count') ?? 0;
        if($paiementToCompare != 0){
            $paiement = ($paiement/$paiementToCompare)*100;
        }else{
            $paiement = '--';
        }
        $facture = DB::table('orders')
                        ->join('order_states', function($join){
                            $join->on('order_states.id', '=', 'orders.order_state_id')->where('order_states.order_type', 'COMMANDE');
                        })
                        ->whereBetween('orders.datecommande', $this_month)
                        ->groupBy('orders.id')
                        ->select(DB::raw('COUNT(*) as count'))
                        ->value('count') ?? 0;
        $factureToCompare = DB::table('orders')
                        ->join('order_states', function($join){
                            $join->on('order_states.id', '=', 'orders.order_state_id')->where('order_states.order_type', 'COMMANDE');
                        })
                        ->join('invoices', function($join){
                            $join->on('invoices.order_id', '=', 'orders.id')->where('invoices.order_id', '!=', 0);
                        })
                        ->whereBetween('orders.datecommande', $this_month)
                        ->select(DB::raw('COUNT(*) as count'))
                        ->value('count') ?? 0;
        if($factureToCompare != 0){
            $facture = ($facture/$factureToCompare)*100;
        }else{
            $facture = '--';
        }
        $totalOrder = DB::table('orders')
                        ->join('order_states', function($join){
                            $join->on('order_states.id', '=', 'orders.order_state_id')->where('order_states.order_type', 'COMMANDE');
                        })
                        ->join('invoices', function($join){
                            $join->on('invoices.order_id', '=', 'orders.id')->where('invoices.order_id', '!=', 0);
                        })
                        ->whereBetween('orders.datecommande', $this_month)
                        ->select(
                            DB::raw('IFNULL(FLOOR(SUM(orders.total)), 0) as amount'), 
                            DB::raw('IFNULL(FLOOR(SUM(orders.nbheure)), 0) as hour'), 
                        )->first();
        $pastTotalOrder = DB::table('orders')
                        ->join('order_states', function($join){
                            $join->on('order_states.id', '=', 'orders.order_state_id')->where('order_states.order_type', 'COMMANDE');
                        })
                        ->join('invoices', function($join){
                            $join->on('invoices.order_id', '=', 'orders.id')->where('invoices.order_id', '!=', 0);
                        })
                        ->whereBetween('orders.datecommande', $last_month)
                        ->select(
                            DB::raw('IFNULL(FLOOR(SUM(orders.total)), 0) as amount'), 
                            DB::raw('IFNULL(FLOOR(SUM(orders.nbheure)), 0) as hour'), 
                        )->first();
        $this_week = [ Carbon::now()->startOfWeek()->startOfDay()->toDateTimeString(), Carbon::now()->endOfWeek()->endOfDay()->toDateTimeString()];
        $events = Event::join('event_statuses', 'events.event_status_id', '=', 'event_statuses.id')
            ->whereBetween('events.datedebut', $this_week)
            ->where('affiliate_id', auth()->user()->affiliate_id)
            ->groupBy('event_statuses.id')
            ->select( 
                DB::raw('COUNT(*) as countOfEvent'), 'event_statuses.name as status'
             )->orderBy('countOfEvent', 'DESC')->get();
        $campagne = DB::table('campagnes')
                    ->whereBetween('datelancement', $this_week)
                    ->select(
                        'name', 
                        DB::raw('DATE_FORMAT(datelancement, "%d/%m/%y") as date')
                    )->get();
        return response()->json([
            'content'           =>DB::table('homenews')->where('affiliat_id', auth()->user()->affiliate_id)->value('code'),
            'devisAFaire'       => $devisAFaire,
            'devisAttenteClient'=> $devisAttenteClient,
            'paiement'          => $paiement,
            'facture'           => $facture,
            'totalOrder'        => $totalOrder->amount,
            'pastTotalOrder'    => $pastTotalOrder->amount,
            'totalHour'         => $totalOrder->hour,
            'pastTotalHour'     => $pastTotalOrder->hour,
            'events'            => $events,
            'campagne'            => $campagne,
        ]);
    }
}