<?php

namespace App\Models;

use App\Models\Tax;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class InvoiceDetail extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'invoice_details';
    protected $guarded = ['id'];
    protected $with = ['tax'];

    public function tax() 
    {
        return $this->belongsTo(Tax::class, 'tax_id');
    }

}
