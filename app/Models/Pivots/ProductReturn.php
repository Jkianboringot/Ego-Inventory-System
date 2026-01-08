<?php

namespace App\Models\Pivots;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductReturn extends Pivot
{
    use SoftDeletes;

    protected $table = 'product_return';

    protected $fillable = [
        'product_id',
        'return_item_id',
        'quantity',
        'unit_price',
        'adds_on'
    ];

    public $timestamps = false;
}
