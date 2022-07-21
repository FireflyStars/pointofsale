<?php

namespace App\Models;

use App\Models\Product;
use App\Models\ProductDocumentType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductDocument extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'product_documents';
    protected $guarded = ['id'];

    public function type() 
    {
        return $this->belongsTo(ProductDocumentType::class, 'product_documents_type_id');
    }

    public function product() 
    {
        return $this->belongsTo(Product::class);
    }

}
