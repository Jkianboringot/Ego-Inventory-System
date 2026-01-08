<?php

namespace App\Models\Pivots;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductSale extends Pivot
{
    use SoftDeletes;

    protected $table = 'product_sale';

    protected $fillable = [
        'product_id',
        'sale_id',
        'quantity',
    ];

    public $timestamps = false;
}
