<?php

namespace App\Models\Pivots;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\SoftDeletes;

class AddProductToList extends Pivot
{
    use SoftDeletes;

    protected $table = 'add_products_to_list';

    protected $fillable = [
        'product_id',
        'add_product_id',
        'quantity',
    ];

    public $timestamps = false;
}
