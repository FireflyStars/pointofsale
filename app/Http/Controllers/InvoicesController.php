<?php

namespace App\Http\Controllers;

use App\Models\Tax;
use App\Models\Invoice;
use App\Models\Customer;
use Illuminate\Http\Request;
use App\Models\InvoiceNumber;
use App\Models\InvoiceHistory;
use App\Http\Controllers\EntiteController;
use App\Models\InvoiceDetail;

class InvoicesController extends Controller
{

    
    public function search(Request $request) 
    {

        $invoices = Invoice::query();

        $customer_id = $request->customer_id;
        $search = $request->search;
        
        $invoices = $invoices
                    ->leftJoin('customers', 'customers.id', '=', 'invoices.customer_id')
                    ->leftJoin('taxes', 'customers.taxe_id', '=', 'taxes.id')
                    ->select( 
                        'customers.company', 
                        'customers.raisonsociale',
                        'customers.telephone', 
                        'customers.email', 
                        'invoices.montant',
                        'invoices.reference',
                        'invoices.dateecheance',
                        'invoices.id as invoice_id',
                        'invoices.order_id',
                    )
                    ->where('invoice_type_id', 1)
                    ->where('invoice_state_id', '>', 1)
                    ->where('customers.id', $customer_id)
                    ->where(function($query) use ($search) {
                        $query->where('invoices.reference', 'like', '%' . $search . '%')
                        ->orWhere('customers.raisonsociale', 'like', '%' . $search . '%')
                        ->orWhere('customers.raisonsociale2', 'like', '%' . $search . '%')
                        ->orWhere('customers.company', 'like', '%' . $search . '%');
                    });

        return response()->json([
            'data'  => $request->has('showmore') ? $invoices->get() : $invoices->take(5)->get(),
            'total' => $invoices->count()
        ]);

    }

    public function create(Request $request) 
    {

        $invoice = Invoice::find($request->invoice_id);
        $type = $request->invoice_type;

        $new_invoice = Invoice::create([
            'customer_id' => $request->customer_id,
            'order_id' => $request->order_id,
            'invoice_id_avoir' => $invoice->id,
            'responsable_id' => $request->user()->id,
            'affiliate_id' => $request->user()->affiliate_id,
            'invoice_type_id' => $type == 'avoir' ? 3 : 1,
            'invoice_state_id' => 1,
            'montant' => $invoice->montant,
            'pourcentage' => $invoice->pourcentage,
            'dateecheance' => $invoice->dateecheance,
            'order_invoice_id' => $invoice->order_invoice_id,
            'lang_id' => 1,
        ]);

        $invoice_number = (new InvoiceNumber)->getInvoiceNumber($new_invoice->id);

        $new_invoice->reference = $invoice_number;
        $new_invoice->save();

        InvoiceHistory::create([
            'user_id' => $request->user()->id,
            'invoice_id' => $new_invoice->id,
            'invoice_state_id' => 1
        ]);

        $customer = Customer::find($new_invoice->customer_id);

        $customer_address = (new EntiteController)->get_customer_address($customer);
        $customer_contact = $this->get_customer_contact($customer);

        return response()->json([
            'invoice' => $new_invoice->load([
                'customer', 
                'customer.paiement',
                'details' => function($query) {
                    $query->latest('created_at')->get();
                }, 
                'order'
            ]),
            'customer_address' => $customer_address,
            'customer_contact' => $customer_contact
        ]);

    }

    public function create_new_invoice(Request $request) 
    {

        $type = $request->invoice_type;

        $new_invoice = Invoice::create([
            'customer_id' => $request->customer_id,
            'order_id' => 0,
            'invoice_id_avoir' => 0,
            'responsable_id' => $request->user()->id,
            'affiliate_id' => $request->user()->affiliate_id,
            'invoice_type_id' => $type == 'avoir' ? 3 : 1,
            'invoice_state_id' => 1,
            'montant' => 0,
            'pourcentage' => 0,
            'dateecheance' => null,
            'order_invoice_id' => 0,
            'lang_id' => 1,
        ]);

        $invoice_number = (new InvoiceNumber)->getInvoiceNumber($new_invoice->id);

        $new_invoice->reference = $invoice_number;
        $new_invoice->save();

        InvoiceHistory::create([
            'user_id' => $request->user()->id,
            'invoice_id' => $new_invoice->id,
            'invoice_state_id' => 1
        ]);

        $customer = Customer::find($new_invoice->customer_id);

        $customer_address = (new EntiteController)->get_customer_address($customer);
        $customer_contact = $this->get_customer_contact($customer);

        return response()->json([
            'invoice' => $new_invoice->load([
                'customer', 
                'customer.paiement',
                'details' => function($query) {
                    $query->latest('created_at')->get();
                }, 
                'order'
            ]),
            'customer_address' => $customer_address,
            'customer_contact' => $customer_contact
        ]);

    }

    private function get_customer_contact(Customer $customer) 
    {

        return $customer->contacts()
            ->select('firstname', 'email', 'mobile')
            ->take(1)
            ->latest('created_at')
            ->first();

    }

    public function get_tax_list() 
    {
        return response()->json(
            Tax::all()
        );
    }

    public function create_ligne(Request $request, Invoice $invoice) 
    {
        
        $detail = $invoice->details()->create([
            'description' => $request->description,
            'montant' => $request->montant,
            'tax_id'  => $request->tax,
            'comment' => $request->comment 
        ]);

        return response()->json($detail->load('tax'));

    }

    public function delete_ligne(InvoiceDetail $invoice, Request $request) 
    {
        
        $invoice->delete();

        $invoice_id = $request->invoice_id;
        $invoice = Invoice::find($invoice_id);

        return response()->json(
            $invoice->details()
            ->latest('created_at')
            ->get()
        );

    }

}
