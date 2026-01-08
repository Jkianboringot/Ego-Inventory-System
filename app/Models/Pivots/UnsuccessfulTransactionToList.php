<?php

namespace App\Models\Pivots;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\SoftDeletes;

class UnsuccessfulTransactionToList extends Pivot
{

    protected $table = 'unsuccessful_transactions_to_list';

    protected $fillable = [
        'product_id',
        'unsuccessful_transaction_id',
        'quantity',
    ];

    public $timestamps = false;
}
