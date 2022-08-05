<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Order;
use App\Models\Invoice;
use App\Models\Paiement;
use App\Models\Event;
use Illuminate\Support\Facades\DB;

class StatisticController extends Controller
{
    //
    public function index(Request $request){
        $customFilter   =   $request->post('customFilter');
        // $customFilter   =   true;
        $startDate      =   $request->post('startDate');
        // $startDate      =   '2022-07-01';
        $endDate        =   $request->post('endDate');
        // $endDate        =   '2022-07-31';
        $dateRangeType  =   $request->post('dateRangeType');
        $compareCustomFilter    =   $request->post('compareCustomFilter');
        $compareStartDate       =   $request->post('compareStartDate');
        // $compareStartDate       =   '2021-07-01';
        $compareEndDate         =   $request->post('compareEndDate');
        // $compareEndDate         =   '2021-07-31';
        $compareMode            =   $request->post('compareMode');
        // $compareMode            =   'year';

        $period         = [ Carbon::parse($startDate)->startOfDay()->toDateTimeString(), Carbon::parse($endDate)->endOfDay()->toDateTimeString() ];
        if(!$compareCustomFilter){
            if($compareMode == 'year')
                $past_period    = [ Carbon::parse($startDate)->subYear(1)->startOfDay()->toDateTimeString(), Carbon::parse($endDate)->subYear(1)->endOfDay()->toDateTimeString() ];
            else
                $past_period    = [ Carbon::parse($startDate)->subMonth(1)->startOfDay()->toDateTimeString(), Carbon::parse($endDate)->subMonth(1)->endOfDay()->toDateTimeString() ];
        }else{
            $past_period = [ Carbon::parse($compareStartDate)->startOfDay()->toDateTimeString(), Carbon::parse($compareEndDate)->endOfDay()->toDateTimeString() ];
        }

        // new code added by YH
        if( !$customFilter ){
            $start_first_quarter_day = Carbon::now()->startOfYear();
            $end_first_quarter_day = Carbon::parse($start_first_quarter_day)->lastOfQuarter();
            if( $dateRangeType == 'Today' ){
                $period = [Carbon::now()->startOfDay()->toDateTimeString(), Carbon::now()->endOfDay()->toDateTimeString()];
                if(!$compareCustomFilter && $compareMode == 'year'){
                    $past_period = [Carbon::now()->subYear(1)->startOfDay()->toDateTimeString(), Carbon::now()->subYear(1)->endOfDay()->toDateTimeString()];
                }
                if(!$compareCustomFilter && $compareMode == 'month'){
                    $past_period = [Carbon::now()->subMonth(1)->startOfDay()->toDateTimeString(), Carbon::now()->subMonth(1)->endOfDay()->toDateTimeString()];
                }
            }else if ( $dateRangeType == 'Yesterday' ){
                $period = [Carbon::yesterday()->startOfDay()->toDateTimeString(), Carbon::yesterday()->endOfDay()->toDateTimeString()];
                if($compareMode == 'year'){
                    $past_period = [Carbon::yesterday()->subYear()->startOfDay()->toDateTimeString(), Carbon::yesterday()->subYear()->endOfDay()->toDateTimeString()];
                }else{
                    $past_period = [Carbon::yesterday()->subMonth()->startOfDay()->toDateTimeString(), Carbon::yesterday()->subMonth()->endOfDay()->toDateTimeString()];
                }
            }else if ( $dateRangeType == 'Last 7 days' ){
                $period = [Carbon::now()->subDays(7)->startOfDay()->toDateTimeString(), Carbon::now()->endOfDay()->toDateTimeString()];
                if(!$compareCustomFilter && $compareMode == 'year'){
                    $past_period = [Carbon::now()->subYear()->subDays(7)->startOfDay()->toDateTimeString(), Carbon::now()->subYear()->endOfDay()->toDateTimeString()];
                }else{
                    $past_period = [Carbon::now()->subMonth()->subDays(7)->startOfDay()->toDateTimeString(), Carbon::now()->subMonth()->endOfDay()->toDateTimeString()];
                }
            }else if ( $dateRangeType == 'Last 90 days' ){
                $period = [Carbon::now()->subDays(90)->startOfDay()->toDateTimeString(), Carbon::now()->endOfDay()->toDateTimeString()];

                if(!$compareCustomFilter && $compareMode == 'year'){
                    $past_period = [Carbon::now()->subYear()->subDays(90)->startOfDay()->toDateTimeString(), Carbon::now()->subYear()->endOfDay()->toDateTimeString()];
                }else{
                    $past_period = [Carbon::now()->subMonth()->subDays(90)->startOfDay()->toDateTimeString(), Carbon::now()->subMonth()->endOfDay()->toDateTimeString()];
                }
            }else if ( $dateRangeType == 'Last Month' ){
                $period = [Carbon::now()->subMonth()->startOfDay()->toDateTimeString(), Carbon::now()->endOfDay()->toDateTimeString()];
                if(!$compareCustomFilter && $compareMode == 'year'){
                    $past_period = [Carbon::now()->subYear()->subMonth()->startOfDay()->toDateTimeString(), Carbon::now()->subYear()->endOfDay()->toDateTimeString()];
                }else{
                    $past_period = [Carbon::now()->subMonth(2)->startOfDay()->toDateTimeString(), Carbon::now()->subMonth(1)->endOfDay()->toDateTimeString()];
                }
            }else if ( $dateRangeType == 'Year to date' ){
                $period = [Carbon::now()->startOfYear()->toDateTimeString(), Carbon::now()->endOfDay()->toDateTimeString()];
                if(!$compareCustomFilter && $compareMode == 'year'){
                    $past_period = [Carbon::now()->subYear()->startOfYear()->toDateTimeString(), Carbon::now()->subYear()->endOfDay()->toDateTimeString()];
                }else{
                    $past_period = [Carbon::now()->subMonth()->startOfMonth()->toDateTimeString(), Carbon::now()->endOfDay()->toDateTimeString()];
                }
            }else if ( $dateRangeType == '4th Quarter' ){
                $period = [Carbon::parse($start_first_quarter_day)->addMonths(9)->startOfDay()->toDateTimeString(), Carbon::parse($end_first_quarter_day)->addMonths(9)->endOfDay()->toDateTimeString()];
                if(!$compareCustomFilter && $compareMode == 'year')
                    $past_period = [Carbon::parse($start_first_quarter_day)->subYear()->addMonths(9)->startOfDay()->toDateTimeString(), Carbon::parse($end_first_quarter_day)->subYear()->addMonths(9)->endOfDay()->toDateTimeString()];
                else
                    $past_period = [Carbon::parse($start_first_quarter_day)->subMonth()->addMonths(9)->startOfDay()->toDateTimeString(), Carbon::parse($end_first_quarter_day)->subMonth()->addMonths(9)->endOfDay()->toDateTimeString()];
            }else if ( $dateRangeType == '3rd Quarter' ){
                $period = [Carbon::parse($start_first_quarter_day)->addMonths(6)->startOfDay()->toDateTimeString(), Carbon::parse($end_first_quarter_day)->addMonths(6)->endOfDay()->toDateTimeString()];
                if(!$compareCustomFilter && $compareMode == 'year')
                    $past_period = [Carbon::parse($start_first_quarter_day)->subYear()->addMonths(6)->startOfDay()->toDateTimeString(), Carbon::parse($end_first_quarter_day)->subYear()->addMonths(6)->endOfDay()->toDateTimeString()];
                else
                    $past_period = [Carbon::parse($start_first_quarter_day)->subMonth()->addMonths(6)->startOfDay()->toDateTimeString(), Carbon::parse($end_first_quarter_day)->subMonth()->addMonths(6)->endOfDay()->toDateTimeString()];
            }else if ( $dateRangeType == '2nd Quarter' ){
                $period = [Carbon::parse($start_first_quarter_day)->addMonths(3)->startOfDay()->toDateTimeString(), Carbon::parse($end_first_quarter_day)->addMonths(3)->endOfDay()->toDateTimeString()];
                if(!$compareCustomFilter && $compareMode == 'year')
                    $past_period = [Carbon::parse($start_first_quarter_day)->subYear()->addMonths(3)->startOfDay()->toDateTimeString(), Carbon::parse($end_first_quarter_day)->subYear()->addMonths(3)->endOfDay()->toDateTimeString()];
                else
                    $past_period = [Carbon::parse($start_first_quarter_day)->subMonth()->addMonths(3)->startOfDay()->toDateTimeString(), Carbon::parse($end_first_quarter_day)->subMonth()->addMonths(3)->endOfDay()->toDateTimeString()];
            }else{
                $start = Carbon::now()->subYear()->startOfYear();
                $period = [$start_first_quarter_day->toDateTimeString(), $end_first_quarter_day->toDateTimeString()];
                if(!$compareCustomFilter && $compareMode == 'year')
                    $past_period = [Carbon::now()->subYear()->startOfYear()->toDateTimeString(), Carbon::now()->subYear()->lastOfQuarter()->toDateTimeString()];
                else
                    $past_period = [Carbon::now()->subMonth()->startOfMonth()->toDateTimeString(), Carbon::now()->endOfDay()->toDateTimeString()];
            }
        }      
          
        $salesByOrigin = Order::join('order_states', 'orders.order_state_id', '=', 'order_states.id')
            ->where('order_states.order_type', 'COMMANDE')
            ->join('customers', function($join){
                $join->on('customers.id', '=', 'orders.customer_id')->where('orders.customer_id', '!=', 0);
            })->rightJoin('customer_origins', function($join){
                $join->on('customer_origins.id', '=', 'customers.customer_origin_id')->where('customers.customer_origin_id', '!=', 0);
            })
            ->whereBetween('orders.datecommande', $period)
            ->select(
                DB::raw('IFNULL(FLOOR(SUM(orders.total)), 0) as amount'), 'customer_origins.name as origin', 'customer_origins.color'
            )->groupBy('origin')
            ->orderBy('amount', 'DESC')
            ->get();

        $salesByOriginTotal = $salesByOrigin->sum('amount');

        $salesByOriginTotalToCompare = Order::join('order_states', 'orders.order_state_id', '=', 'order_states.id')
            ->where('order_states.order_type', 'COMMANDE')
            ->join('customers', function($join){
                $join->on('customers.id', '=', 'orders.customer_id')->where('orders.customer_id', '!=', 0);
            })->rightJoin('customer_origins', function($join){
                $join->on('customer_origins.id', '=', 'customers.customer_origin_id')->where('customers.customer_origin_id', '!=', 0);
            })
            ->whereBetween('orders.datecommande', $past_period)
            ->select(
                DB::raw('IFNULL(FLOOR(SUM(orders.total)), 0) as amount')
            )->value('amount');

        $salesByClient = Order::join('order_states', 'orders.order_state_id', '=', 'order_states.id')
            ->where('order_states.order_type', 'COMMANDE')
            ->rightJoin('customers', function($join){
                $join->on('customers.id', '=', 'orders.customer_id')->where('orders.customer_id', '!=', 0);
            })->select(
                DB::raw('IFNULL(FLOOR(SUM(orders.total)), 0) as amount'), DB::raw('CONCAT(customers.firstname, " ", customers.name) as client'),
                DB::raw('IFNULL(FLOOR(SUM(orders.nbheure)), 0) as hour')
            )
            ->whereBetween('orders.datecommande', $period)
            ->groupBy('client')
            ->orderBy('amount', 'DESC')
            ->get();

        $salesByClientTotalToCompare = Order::join('order_states', 'orders.order_state_id', '=', 'order_states.id')
            ->where('order_states.order_type', 'COMMANDE')
            ->rightJoin('customers', function($join){
                $join->on('customers.id', '=', 'orders.customer_id')->where('orders.customer_id', '!=', 0);
            })->select(
                DB::raw('IFNULL(FLOOR(SUM(orders.nbheure)), 0) as hour')
            )
            ->whereBetween('orders.datecommande', $past_period)
            ->value('hour');
        $salesByClientTotal = $salesByClient->sum('hour');

        $salesByClientNew = [];
        foreach ($salesByClient as $key => $item) {
            if($key <= 5){
                $salesByClientNew[] = [ 'amount'=> $item->amount, 'client'=> $item->client ];
            }else{
                $salesByClientNew[5]['amount'] += $item->amount;
                $salesByClientNew[5]['client'] = 'Autres Clients';
            }
        }
        // avg of sales
        $avgSale = Order::join('order_states', 'orders.order_state_id', '=', 'order_states.id')
                        ->where('order_states.order_type', 'COMMANDE')
                        ->whereBetween('orders.datecommande', $period)
                        ->select(
                            DB::raw('IFNULL(FLOOR(AVG(orders.total)), 0) as avg')
                        )
                        ->value('avg');
        $avgSaleToCompare = Order::join('order_states', 'orders.order_state_id', '=', 'order_states.id')
                        ->where('order_states.order_type', 'COMMANDE')
                        ->whereBetween('orders.datecommande', $past_period)
                        ->select(
                            DB::raw('IFNULL(FLOOR(AVG(orders.total)), 0) as avg')
                        )
                        ->value('avg');
        //sales by customer category
        $salesByCustCat = Order::join('order_states', function($join){
                $join->on('orders.order_state_id', '=', 'order_states.id')->where('order_states.order_type', 'COMMANDE');
            })
            ->join('customers', function($join){
                $join->on('orders.customer_id', '=', 'customers.id')->where('orders.customer_id', '!=', 0);
            })
            ->join('customer_categories', 'customer_categories.id', '=', 'customers.customer_categories_id')
            ->whereBetween('orders.datecommande', $period)
            ->groupBy('customer_categories.id')
            ->orderBy('amount', 'DESC')
            ->select(
                DB::raw('FLOOR(SUM(total)) AS amount'), 'customer_categories.name'
            )->limit(2)->get();
        
        $invoicePaid = Invoice::whereBetween('dateecheance', $period)
                        ->select(DB::raw('COUNT(*) as invoice_paid'))
                        ->where('invoice_state_id', 3)
                        ->value('invoice_paid');
        $nbinvoice = Invoice::join('invoice_types', 'invoice_types.id', '=', 'invoices.invoice_type_id')
                        ->whereBetween('invoices.dateecheance', $period)
                        ->select(DB::raw('COUNT(*) as nbinvoice'))
                        ->where('invoice_types.sign', 1)
                        ->value('nbinvoice');

        $nbOrderInvoice = Order::join('order_states', function($join){
                            $join->on('order_states.id', '=', 'orders.order_state_id')
                                ->where('order_states.order_type', 'COMMANDE');
                        })
                        ->whereBetween('orders.datecommande', $period)
                        ->whereIn('order_state_id', [11,12,13])
                        ->select(DB::raw('COUNT(*) as nbOrderInvoice'))
                        ->value('nbOrderInvoice');
        
        $nbOrder = Order::join('order_states', function($join){
                            $join->on('order_states.id', '=', 'orders.order_state_id')
                                ->where('order_states.order_type', 'COMMANDE');
                        })
                        ->whereBetween('orders.datecommande', $period)
                        ->select(DB::raw('COUNT(*) as nbOrder'))
                        ->value('nbOrder');
        // sales by date
        $salesByDate = Order::join('order_states', function($join){
                    $join->on('order_states.id', '=', 'orders.order_state_id')
                        ->where('order_states.order_type', 'COMMANDE');
                })
                ->select('total as amount', 'datecommande as date')
            ->whereNotNull('datecommande')
            ->whereBetween('datecommande', $period)
            ->get();
        $salesByDatePast = Order::join('order_states', function($join){
                    $join->on('order_states.id', '=', 'orders.order_state_id')
                        ->where('order_states.order_type', 'COMMANDE');
                })
                ->select('total as amount', 'datecommande as date')
            ->whereNotNull('datecommande')
            ->whereBetween('datecommande', $past_period)
            ->get();

        // hours by date
        $hoursByDate = Order::join('order_states', function($join){
                $join->on('order_states.id', '=', 'orders.order_state_id')
                    ->where('order_states.order_type', 'COMMANDE');
            })
            ->whereNotNull('datecommande')
            ->whereBetween('datecommande', $period)
            ->select('nbheure as amount', 'datecommande as date')
            ->get();
        $hoursByDatePast = Order::join('order_states', function($join){
                $join->on('order_states.id', '=', 'orders.order_state_id')
                    ->where('order_states.order_type', 'COMMANDE');
            })
            ->whereNotNull('datecommande')
            ->whereBetween('datecommande', $past_period)
            ->select('nbheure as amount', 'datecommande as date')
            ->get();

        // Facture €
        $facture = Invoice::join('invoice_types', 'invoice_types.id', '=', 'invoices.invoice_type_id')
                    ->whereBetween('invoices.dateecheance', $period)
                    ->select(DB::raw('invoices.montant * invoice_types.sign as amount'), 'invoices.dateecheance as date')
                    ->whereNotNull('invoices.dateecheance')
                    ->get();
        $facturePast = Invoice::join('invoice_types', 'invoice_types.id', '=', 'invoices.invoice_type_id')
                    ->whereBetween('invoices.dateecheance', $past_period)
                    ->select(DB::raw('invoices.montant * invoice_types.sign as amount'), 'invoices.dateecheance as date')
                    ->whereNotNull('invoices.dateecheance')
                    ->get();

        $paiement = Paiement::whereBetween('datepaiement', $period)
                    ->select('montantpaiement as amount', 'datepaiement as date')
                    ->whereNotNull('datepaiement')
                    ->get();
        $paiementPast = Paiement::whereBetween('datepaiement', $past_period)
                    ->select('montantpaiement as amount', 'datepaiement as date')
                    ->whereNotNull('datepaiement')
                    ->get();
        $salesByMarge = [];
        $salesByMargePast = [];
        $salesByUser = Order::join('order_states', function($join){
                $join->on('order_states.id', '=', 'orders.order_state_id')
                    ->where('order_states.order_type', 'COMMANDE');
            })
            ->join('users', 'users.id', '=', 'orders.responsable_id')
            ->whereNotNull('orders.datecommande')
            ->whereBetween('orders.datecommande', $period)
            ->select( 
                DB::raw('FLOOR(SUM(orders.total)) as amount'),
                DB::raw('FLOOR(SUM(orders.nbheure)) as hour'),
                'users.name', 'users.id'
             )
             ->groupBy('users.id')->orderBy('amount')->limit(5)->get();
        foreach ($salesByUser as $item) {
            $pastSaleForUser = Order::join('order_states', function($join){
                    $join->on('order_states.id', '=', 'orders.order_state_id')
                        ->where('order_states.order_type', 'COMMANDE');
                })
                ->whereNotNull('orders.datecommande')
                ->whereBetween('orders.datecommande', $past_period)
                ->where('orders.responsable_id', $item->id)
                ->select( 
                    DB::raw('FLOOR(SUM(orders.total)) as amount'),
                    DB::raw('FLOOR(SUM(orders.nbheure)) as hour')
                )
                ->groupBy('orders.responsable_id')->first();
            $item->pastAmount = $pastSaleForUser ? $pastSaleForUser->amount : 0;
            $item->pastHour = $pastSaleForUser ? $pastSaleForUser->hour : 0;
        }
        // sales by commande
        $salesByCommande = Order::join('order_states', function($join){
            $join->on('order_states.id', '=', 'orders.order_state_id')
                ->where('order_states.order_type', 'COMMANDE');
        })
        ->join('customers', 'customers.id', '=', 'orders.customer_id')
        ->whereBetween('orders.datecommande', $period)
        ->select( 
            'orders.total as amount', 'orders.nbheure as hour',
            'customers.name', 'orders.id'
         )
         ->orderBy('amount')->limit(5)->get();

        //  devis 
        $devisByStatus = Order::join('order_states', function($join){
                $join->on('order_states.id', '=', 'orders.order_state_id')
                    ->where('order_states.order_type', 'DEVIS');
            })
            ->whereBetween('orders.datecommande', $period)
            ->select(
                DB::raw('COUNT(*) AS countOfDevis'), 'order_states.name as status', 'order_states.id'
            )
            ->groupBy('order_states.id')->orderBy('order_states.id', 'ASC')->limit(5)->get();
        foreach ($devisByStatus as $item) {
            $item->pastCountOfDevis = Order::join('order_states', function($join){
                    $join->on('order_states.id', '=', 'orders.order_state_id')
                        ->where('order_states.order_type', 'DEVIS');
                })
                ->whereBetween('orders.datecommande', $past_period)
                ->where('order_states.id', $item->id)
                ->select(
                    DB::raw('COUNT(*) AS countOfDevis')
                )
                ->groupBy('order_states.id')->value('countOfDevis') ?? 0;
        }
        $totalDevisCount = $devisByStatus->sum('pastCountOfDevis');
        $totalDevisCountPast = $devisByStatus->sum('countOfDevis');

        $devisByUser = Order::join('order_states', function($join){
                $join->on('order_states.id', '=', 'orders.order_state_id')
                    ->where('order_states.order_type', 'DEVIS');
            })
            ->join('users', 'users.id', '=', 'orders.responsable_id')
            ->whereBetween('orders.datecommande', $period)
            ->select(
                DB::raw('COUNT(*) AS countOfDevis'), 'users.name', 'users.id'
            )
            ->groupBy('users.id')->orderBy('countOfDevis', 'DESC')->limit(5)->get();
        foreach ($devisByUser as $item) {
            $item->pastCountOfDevis = Order::join('order_states', function($join){
                    $join->on('order_states.id', '=', 'orders.order_state_id')
                        ->where('order_states.order_type', 'DEVIS');
                })
                ->whereBetween('orders.datecommande', $past_period)
                ->where('orders.responsable_id', $item->id)
                ->select(
                    DB::raw('COUNT(*) AS countOfDevis')
                )
                ->groupBy('orders.responsable_id')->value('countOfDevis') ?? 0;
        }

        // action commercial by status
        $eventsByStatus = Event::join('event_statuses', 'events.event_status_id', '=', 'event_statuses.id')
            ->whereBetween('events.datedebut', $period)
            ->groupBy('event_statuses.id')
            ->select( 
                DB::raw('COUNT(*) as countOfEvent'), 'event_statuses.name as status'
             )->orderBy('countOfEvent', 'DESC')->limit(5)->get();
        foreach ($eventsByStatus as $item) {
            $item->pastCountOfEvent = Event::whereBetween('events.datedebut', $past_period)
                    ->groupBy('events.event_status_id')
                    ->select( 
                        DB::raw('COUNT(*) as countOfEvent')
                    )->value('countOfEvent') ?? 0;
        }

        // action commercial by type
        $eventsByType = Event::join('event_types', 'events.event_type_id', '=', 'event_types.id')
            ->whereBetween('events.datedebut', $period)
            ->groupBy('event_types.id')
            ->select( 
                DB::raw('COUNT(*) as countOfEvent'), 'event_types.name as type'
             )->orderBy('countOfEvent', 'DESC')->limit(5)->get();
        foreach ($eventsByType as $item) {
            $item->pastCountOfEvent = Event::whereBetween('events.datedebut', $past_period)
                    ->groupBy('events.event_type_id')
                    ->select( 
                        DB::raw('COUNT(*) as countOfEvent')
                    )->value('countOfEvent') ?? 0;
        }
        $totalEventCount = $eventsByType->sum('countOfEvent');
        $totalEventCountPast = $eventsByType->sum('pastCountOfEvent');        
        return response()->json([
            'salesByOrigin'                 => $salesByOrigin,
            'salesByOriginTotal'            => $salesByOriginTotal,
            'salesByOriginTotalToCompare'   => $salesByOriginTotalToCompare,
            
            'salesByClient'                 => $salesByClientNew,
            'salesByClientTotal'            => $salesByClientTotal,
            'salesByClientTotalToCompare'   => $salesByClientTotalToCompare,

            'avgSale'           => $avgSale,
            'avgSaleToCompare'  => $avgSaleToCompare,
            'salesByCustCat'    => $salesByCustCat,
            'paiement'          => $nbinvoice != 0 ? number_format(($invoicePaid / $nbinvoice)*100) : '--',
            'facture'           => $nbOrder != 0 ? number_format(($nbOrderInvoice / $nbOrder)*100) : '--',
            
            'series1Data'   => $salesByDate,
            'series2Data'   => $hoursByDate,
            'series3Data'   => $facture,
            'series4Data'   => $paiement,
            'series5Data'   => $salesByMarge,
            'series6Data'   => $salesByDatePast,
            'series7Data'   => $hoursByDatePast,
            'series8Data'   => $facturePast,
            'series9Data'   => $paiementPast,
            'series10Data'  => $salesByMargePast,

            'salesByUser'       => $salesByUser,
            'salesByCommande'   => $salesByCommande,

            'devisByStatus'     => $devisByStatus,
            'devisByUser'       => $devisByUser,
            'totalDevisCount'   => $totalDevisCount,
            'totalDevisCountPast' => $totalDevisCountPast,

            'eventsByStatus'  => $eventsByStatus,
            'eventsByType'    => $eventsByType,
            'totalEventCount'    => $totalEventCount,
            'totalEventCountPast'=> $totalEventCountPast,
        ]);
    }
}
