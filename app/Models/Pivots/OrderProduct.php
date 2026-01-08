<?php

namespace App\Models\Pivots;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderProduct extends Pivot
{
    use SoftDeletes;

    protected $table = 'order_product';

    protected $fillable = [
        'product_id',
        'order_id',
        'quantity',
        'unit_price'
    ];

    public $timestamps = false;
}
