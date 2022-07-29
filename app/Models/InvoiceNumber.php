<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InvoiceNumber extends Model
{
    use HasFactory;

    public static function getInvoiceNumber($invoice_id){
        $invoice_number=InvoiceNumber::where('invoice_id','=',$invoice_id)->first();
        if($invoice_number==null){//make a new invoice number
            $in=Invoice::find($invoice_id);
            if($in==null)
            throw new Exception("Invoice not found");

            $user=Auth::user();
            if($user->affiliate_id!=$in->affiliate_id)
            throw new Exception('Invoice is not in the same affiliate as the user');

            if($in->invoice_type_id==2)
            throw new Exception('Remise does not have an invoice number');

          
            if($in->invoice_type_id==1||$in->invoice_type_id==4){//facture ou avenant
                $increment=DB::table('invoice_numbers')->select(DB::raw('MAX(num) as increment'))->value('increment');
                $prefix='FA';
            }else if($in->invoice_type_id==3){//avoir
                $increment=DB::table('invoice_numbers')->select(DB::raw('MAX(num_avoir) as increment'))->value('increment');
                $prefix='AV';
            }

            if($increment==null)
            $increment=0;

            $increment++;

            $invoice_number=new InvoiceNumber();
            $invoice_number->invoice_id=$in->id;

            if($in->invoice_type_id==1||$in->invoice_type_id==4){//facture ou avenant
                $invoice_number->num=$increment;
            }else if($in->invoice_type_id==3){//avoir
                $invoice_number->num_avoir=$increment;
            }
            $invoice_number->numinvoice=$prefix.date('dmY').'-'.$increment;
            $invoice_number->save();
            
        }
        //return  invoicenumber
        return $invoice_number->numinvoice;
        

    }
}
