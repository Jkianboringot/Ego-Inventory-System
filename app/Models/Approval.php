<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Approval extends Model
{
    protected $fillable = [
        'user_id',
        'approvable_id',
        'approvable_type',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Polymorphic relation
    public function approvable()
    {
        return $this->morphTo();
    }
}
