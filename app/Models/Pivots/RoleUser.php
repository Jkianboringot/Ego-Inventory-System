<?php

namespace App\Models\Pivots;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\SoftDeletes;

class RoleUser extends Pivot
{
    use SoftDeletes;

    protected $table = 'role_user';

    protected $fillable = [
        'product_id',
        'user_id',
        'quantity',
    ];

    public $timestamps = false;
}
