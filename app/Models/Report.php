<?php

namespace App\Models;

use App\Models\Order;
use App\Models\Template;
use App\Models\Affiliate;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Report extends Model
{

    use HasFactory;
    use SoftDeletes;

    protected $guarded = ['id'];

    protected $casts = [
        'pages'      => 'array',
        'page_files' => 'array'
    ];

    protected $appends = ['page_count'];

    public function order() 
    {
        return $this->belongsTo(Order::class);
    }

    public function user() 
    {
        return $this->belongsTo(User::class);
    }

    public function template() 
    {
        return $this->belongsTo(Template::class, 'template_id');
    }

    public function affiliate() 
    {
        return $this->belongsTo(Affiliate::class);
    }

    public function getPageCountAttribute() 
    {
        if(is_null($this->pages)) return;
        return gettype($this->pages) == 'string' ? count(json_decode($this->pages)) : count($this->pages);
    }

}
