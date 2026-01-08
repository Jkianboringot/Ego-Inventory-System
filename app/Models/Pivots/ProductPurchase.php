<?php

namespace App\Models\Pivots;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductPurchase extends Pivot
{
    use SoftDeletes;

    protected $table = 'product_purchase';

    protected $fillable = [
        'product_id',
        'purchase_id',
        'quantity',
    ];

    public $timestamps = false;
}
