<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class SaisieRapideController extends Controller
{
    /**
     * Get some infos to create pointage
     */
    public function getInfo(){
        $users = DB::table('users')->select('id as value', DB::raw('CONCAT(firstname, " ", name) as display'))->get();
        foreach ($users as $user) {
            $user->display = Str::initials($user->display);
        }
        return response()->json([
            'users'=> $users,
            'types'=> DB::table('pointage_type')->select('id as value', 'name as display')->get()
        ]);
    }
    /**
     * Search saisie orders
     */
    public function searchOrders(Request $request){
        $orders = DB::table('orders')
                    ->join('customers', 'customers.id', '=', 'orders.customer_id')
                    ->select(  'orders.id', 'orders.name', 'customers.raisonsociale as raisonsocial')
                    ->whereNull('orders.deleted_at')
                    ->where('customers.raisonsociale', 'like', '%'.$request->search.'%')
                    ->orWhere('orders.name', 'like', '%'.$request->search.'%')
                    ->orWhere('orders.id', 'like', '%'.$request->search.'%')->take(10)->get();
        return response()->json($orders);
    }
    /**
     * get pointages by date
     */
    public function getPointages(Request $request){
        $pointages = DB::table('pointage')->join('pointage_type', 'pointage_type.id', '=', 'pointage.pointage_type_id')
                    ->join('orders', 'orders.id', '=', 'pointage.order_id')
                    ->join('customers', 'orders.customer_id', '=', 'customers.id')
                    ->join('users', 'users.id', '=', 'pointage.user_id')
                    ->where('datepointage', $request->datepointage)
                    ->select(
                        'pointage.id', 'pointage.order_id as orderId', 'pointage.user_id as userId',
                        DB::raw('CONCAT(users.firstname, " ", users.name) as userName'),
                        'orders.name as orderName', 'customers.raisonsociale as raisonsocila',
                        'pointage.numberh', 'pointage.numberhtransport'
                    )->get();
        return response()->json($pointages);
    }
    /**
     * Create a pointage
     */
    public function create(Request $request){
        DB::table('pointage')->insert([
            'order_id'          => $request->orderId,
            'user_id'           => $request->userId,
            'pointage_type_id'  => $request->typeId,
            'datepointage'      => $request->datepointage,
            'numberh'           => $request->numberh,
            'numberhtransport'  => $request->numberhtransport,
            'affiliate_id'      => auth()->user()->affiliate_id,
        ]);
        $pointages = DB::table('pointage')->join('pointage_type', 'pointage_type.id', '=', 'pointage.pointage_type_id')
                    ->join('orders', 'orders.id', '=', 'pointage.order_id')
                    ->join('customers', 'orders.customer_id', '=', 'customers.id')
                    ->join('users', 'users.id', '=', 'pointage.user_id')
                    ->where('datepointage', $request->datepointage)
                    ->select(
                        'pointage.id', 'pointage.order_id as orderId', 'pointage.user_id as userId',
                        DB::raw('CONCAT(users.firstname, " ", users.name) as userName'),
                        'orders.name as orderName', 'customers.raisonsociale as raisonsocila',
                        'pointage.numberh', 'pointage.numberhtransport'
                    )->get();
        return response()->json($pointages);
    }
    /**
     * soft delete a pointage
     */
    public function delete(Request $request){
        DB::table('pointage')->where('id', $request->pointageId)->update([
            'deleted_at'=> Carbon::now(),
        ]);
        return response()->json(true);
    }
}
