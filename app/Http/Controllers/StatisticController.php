<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Order;
use Illuminate\Support\Facades\DB;

class StatisticController extends Controller
{
    //
    public function index(Request $request){
        $customFilter   =   $request->post('customFilter');
        $startDate      =   $request->post('startDate');
        $endDate        =   $request->post('endDate');
        $dateRangeType  =   $request->post('dateRangeType');
        $compareMode    =   $request->post('compareMode');
        $compareCustomFilter    =   $request->post('compareCustomFilter');
        $compareStartDate       =   $request->post('compareStartDate');
        $compareEndDate         =   $request->post('compareEndDate');
        $compareMode            =   $request->post('compareMode');

        $period         = [ Carbon::parse($startDate)->startOfDay()->toDateTimeString(), Carbon::parse($endDate)->endOfDay()->toDateTimeString() ];
        if(!$compareCustomFilter){
            if($compareMode == 'year')
                $last_period    = [ Carbon::parse($startDate)->subYear(1)->startOfDay()->toDateTimeString(), Carbon::parse($endDate)->subYear(1)->endOfDay()->toDateTimeString() ];
            else
                $last_period    = [ Carbon::parse($startDate)->subMonth(1)->startOfDay()->toDateTimeString(), Carbon::parse($endDate)->subMonth(1)->endOfDay()->toDateTimeString() ];
        }else{
            $last_period = [ Carbon::parse($compareStartDate)->subMonth(1)->startOfDay()->toDateTimeString(), Carbon::parse($compareEndDate)->subMonth(1)->endOfDay()->toDateTimeString() ];
        }

        // new code added by YH
        if( !$customFilter ){
            $start_first_quarter_day = Carbon::now()->startOfYear();
            $end_first_quarter_day = Carbon::parse($start_first_quarter_day)->lastOfQuarter();
            if( $dateRangeType == 'Today' ){
                $period = [Carbon::now()->startOfDay()->toDateTimeString(), Carbon::now()->endOfDay()->toDateTimeString()];
                if(!$compareCustomFilter && $compareMode == 'year'){
                    $last_period = [Carbon::now()->subYear(1)->startOfDay()->toDateTimeString(), Carbon::now()->subYear(1)->endOfDay()->toDateTimeString()];
                }
                if(!$compareCustomFilter && $compareMode == 'month'){
                    $last_period = [Carbon::now()->subMonth(1)->startOfDay()->toDateTimeString(), Carbon::now()->subMonth(1)->endOfDay()->toDateTimeString()];
                }
            }else if ( $dateRangeType == 'Yesterday' ){
                $period = [Carbon::yesterday()->startOfDay()->toDateTimeString(), Carbon::yesterday()->endOfDay()->toDateTimeString()];
                if($compareMode == 'year'){
                    $last_period = [Carbon::yesterday()->subYear()->startOfDay()->toDateTimeString(), Carbon::yesterday()->subYear()->endOfDay()->toDateTimeString()];
                }else{
                    $last_period = [Carbon::yesterday()->subMonth()->startOfDay()->toDateTimeString(), Carbon::yesterday()->subMonth()->endOfDay()->toDateTimeString()];
                }
            }else if ( $dateRangeType == 'Last 7 days' ){
                $period = [Carbon::now()->subDays(7)->startOfDay()->toDateTimeString(), Carbon::now()->endOfDay()->toDateTimeString()];
                if(!$compareCustomFilter && $compareMode == 'year'){
                    $last_period = [Carbon::now()->subYear()->subDays(7)->startOfDay()->toDateTimeString(), Carbon::now()->subYear()->endOfDay()->toDateTimeString()];
                }else{
                    $last_period = [Carbon::now()->subMonth()->subDays(7)->startOfDay()->toDateTimeString(), Carbon::now()->subMonth()->endOfDay()->toDateTimeString()];
                }
            }else if ( $dateRangeType == 'Last 90 days' ){
                $period = [Carbon::now()->subDays(90)->startOfDay()->toDateTimeString(), Carbon::now()->endOfDay()->toDateTimeString()];

                if(!$compareCustomFilter && $compareMode == 'year'){
                    $last_period = [Carbon::now()->subYear()->subDays(90)->startOfDay()->toDateTimeString(), Carbon::now()->subYear()->endOfDay()->toDateTimeString()];
                }else{
                    $last_period = [Carbon::now()->subMonth()->subDays(90)->startOfDay()->toDateTimeString(), Carbon::now()->subMonth()->endOfDay()->toDateTimeString()];
                }
            }else if ( $dateRangeType == 'Last Month' ){
                $period = [Carbon::now()->subMonth()->startOfDay()->toDateTimeString(), Carbon::now()->endOfDay()->toDateTimeString()];
                if(!$compareCustomFilter && $compareMode == 'year'){
                    $last_period = [Carbon::now()->subYear()->subMonth()->startOfDay()->toDateTimeString(), Carbon::now()->subYear()->endOfDay()->toDateTimeString()];
                }else{
                    $last_period = [Carbon::now()->subMonth(2)->startOfDay()->toDateTimeString(), Carbon::now()->subMonth(1)->endOfDay()->toDateTimeString()];
                }
            }else if ( $dateRangeType == 'Year to date' ){
                $period = [Carbon::now()->startOfYear()->toDateTimeString(), Carbon::now()->endOfDay()->toDateTimeString()];
                if(!$compareCustomFilter && $compareMode == 'year'){
                    $last_period = [Carbon::now()->subYear()->startOfYear()->toDateTimeString(), Carbon::now()->subYear()->endOfDay()->toDateTimeString()];
                }else{
                    $last_period = [Carbon::now()->subMonth()->startOfMonth()->toDateTimeString(), Carbon::now()->endOfDay()->toDateTimeString()];
                }
            }else if ( $dateRangeType == '4th Quarter' ){
                $period = [Carbon::parse($start_first_quarter_day)->addMonths(9)->startOfDay()->toDateTimeString(), Carbon::parse($end_first_quarter_day)->addMonths(9)->endOfDay()->toDateTimeString()];
                if(!$compareCustomFilter && $compareMode == 'year')
                    $last_period = [Carbon::parse($start_first_quarter_day)->subYear()->addMonths(9)->startOfDay()->toDateTimeString(), Carbon::parse($end_first_quarter_day)->subYear()->addMonths(9)->endOfDay()->toDateTimeString()];
                else
                    $last_period = [Carbon::parse($start_first_quarter_day)->subMonth()->addMonths(9)->startOfDay()->toDateTimeString(), Carbon::parse($end_first_quarter_day)->subMonth()->addMonths(9)->endOfDay()->toDateTimeString()];
            }else if ( $dateRangeType == '3rd Quarter' ){
                $period = [Carbon::parse($start_first_quarter_day)->addMonths(6)->startOfDay()->toDateTimeString(), Carbon::parse($end_first_quarter_day)->addMonths(6)->endOfDay()->toDateTimeString()];
                if(!$compareCustomFilter && $compareMode == 'year')
                    $last_period = [Carbon::parse($start_first_quarter_day)->subYear()->addMonths(6)->startOfDay()->toDateTimeString(), Carbon::parse($end_first_quarter_day)->subYear()->addMonths(6)->endOfDay()->toDateTimeString()];
                else
                    $last_period = [Carbon::parse($start_first_quarter_day)->subMonth()->addMonths(6)->startOfDay()->toDateTimeString(), Carbon::parse($end_first_quarter_day)->subMonth()->addMonths(6)->endOfDay()->toDateTimeString()];
            }else if ( $dateRangeType == '2nd Quarter' ){
                $period = [Carbon::parse($start_first_quarter_day)->addMonths(3)->startOfDay()->toDateTimeString(), Carbon::parse($end_first_quarter_day)->addMonths(3)->endOfDay()->toDateTimeString()];
                if(!$compareCustomFilter && $compareMode == 'year')
                    $last_period = [Carbon::parse($start_first_quarter_day)->subYear()->addMonths(3)->startOfDay()->toDateTimeString(), Carbon::parse($end_first_quarter_day)->subYear()->addMonths(3)->endOfDay()->toDateTimeString()];
                else
                    $last_period = [Carbon::parse($start_first_quarter_day)->subMonth()->addMonths(3)->startOfDay()->toDateTimeString(), Carbon::parse($end_first_quarter_day)->subMonth()->addMonths(3)->endOfDay()->toDateTimeString()];
            }else{
                $start = Carbon::now()->subYear()->startOfYear();
                $period = [$start_first_quarter_day->toDateTimeString(), $end_first_quarter_day->toDateTimeString()];
                if(!$compareCustomFilter && $compareMode == 'year')
                    $last_period = [Carbon::now()->subYear()->startOfYear()->toDateTimeString(), Carbon::now()->subYear()->lastOfQuarter()->toDateTimeString()];
                else
                    $last_period = [Carbon::now()->subMonth()->startOfMonth()->toDateTimeString(), Carbon::now()->endOfDay()->toDateTimeString()];
            }
        }        
        $salesByOrigin = Order::join('customers', function($join){
            $join->on('customers.id', '=', 'orders.customer_id')->where('orders.customer_id', '!=', 0);
        })->rightJoin('customer_origins', function($join){
            $join->on('customer_origins.id', '=', 'customers.customer_origin_id')->where('customers.customer_origin_id', '!=', 0);
        })
        // ->whereBetween('orders.created_at', $period)
        ->select(
            DB::raw('IFNULL(FLOOR(SUM(orders.total)), 0) as amount'), 'customer_origins.name as origin', 'customer_origins.color'
        )->groupBy('origin')->orderBy('amount', 'DESC')->get();
        $salesByOriginTotal = $salesByOrigin->sum('amount');

        $salesByOriginTotalToCompare = Order::join('customers', function($join){
            $join->on('customers.id', '=', 'orders.customer_id')->where('orders.customer_id', '!=', 0);
        })->rightJoin('customer_origins', function($join){
            $join->on('customer_origins.id', '=', 'customers.customer_origin_id')->where('customers.customer_origin_id', '!=', 0);
        })
        // ->whereBetween('orders.created_at', $last_period)
        ->select(
            DB::raw('IFNULL(FLOOR(SUM(orders.total)), 0) as amount')
        )->value('amount');

        $salesByClient = Order::rightJoin('customers', function($join){
            $join->on('customers.id', '=', 'orders.customer_id')->where('orders.customer_id', '!=', 0);
        })->select(
            DB::raw('IFNULL(FLOOR(SUM(orders.total)), 0) as amount'), DB::raw('CONCAT(customers.firstname, " ", customers.name) as client')
        )
        // ->whereBetween('orders.created_at', $period)
        ->groupBy('client')->orderBy('amount', 'DESC')->get();

        $salesByClientTotalToCompare = Order::rightJoin('customers', function($join){
            $join->on('customers.id', '=', 'orders.customer_id')->where('orders.customer_id', '!=', 0);
        })->select(
            DB::raw('IFNULL(FLOOR(SUM(orders.total)), 0) as amount')
        )
        // ->whereBetween('orders.created_at', $last_period)
        ->value('amount');
        $salesByClientTotal = $salesByClient->sum('amount');

        $salesByClientNew = [];
        foreach ($salesByClient as $key => $item) {
            if($key <= 5){
                $salesByClientNew[] = [ 'amount'=> $item->amount, 'client'=> $item->client ];
            }else{
                $salesByClientNew[5]['amount'] += $item->amount;
                $salesByClientNew[5]['client'] = 'Autres Clients';
            }
        }
        // sales by date
        $salesByDate = Order::select(
            'total as amount', DB::raw('DATE_FORMAT(created_at, "%Y-%m-%d") as date')
        )
        // ->whereBetween('created_at', $period)
        ->get();

        // hours by date
        $hoursByDate = Order::select(
            'nbheure as amount', DB::raw('DATE_FORMAT(created_at, "%Y-%m-%d") as date')
        )
        // ->whereBetween('created_at', $period)
        ->get();
        return response()->json([
            'salesByOrigin'                 => $salesByOrigin,
            'salesByOriginTotal'            => $salesByOriginTotal,
            'salesByOriginTotalToCompare'   => $salesByOriginTotalToCompare,
            
            'salesByClient'                 => $salesByClientNew,
            'salesByClientTotal'            => $salesByClientTotal,
            'salesByClientTotalToCompare'   => $salesByClientTotalToCompare,

            'series1Data'   => $salesByDate,
            'series2Data'   => $hoursByDate,
            'series3Data'   => $salesByDate,
            'series4Data'   => $salesByDate,
            'series5Data'   => $salesByDate,
            'series6Data'   => $salesByDate,
            'series7Data'   => $salesByDate,
            'series8Data'   => $salesByDate,
            'series9Data'   => $salesByDate,
            'series10Data'   => $salesByDate,
        ]);
    }
}
