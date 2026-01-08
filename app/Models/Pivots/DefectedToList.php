<?php

namespace App\Models\Pivots;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\SoftDeletes;

class DefectedToList extends Pivot
{
    use SoftDeletes;

    protected $table = 'defecteds_to_list';

    protected $fillable = [
        'product_id',
        'defected_id',
        'quantity',
    ];

    public $timestamps = false;
}
