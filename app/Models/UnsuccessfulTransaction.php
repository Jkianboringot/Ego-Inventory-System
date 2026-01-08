<?php

namespace App\Models;

use App\Livewire\Admin\Approvals\AddApproval;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;

class UnsuccessfulTransaction extends Model
{

    use HasFactory;
    protected $fillable = ['status', 'order_id'];

    public function approvals()
    {
        return $this->morphMany(Approval::class, 'approvable');
    }

    public function editRequests()
    {
        return $this->morphMany(EditApproval::class, 'editable');
    }
    function order()
    {
        return $this->belongsTo(Order::class);
    }

    function products()
    {
        return $this->belongsToMany(Product::class, 'unsuccessful_transactions_to_list')
            ->withPivot(['quantity']);;
    }


    protected static function booted()
    {





        static::deleting(function ($unsuccessfull) {
            DB::transaction(function () use ($unsuccessfull) {
                \App\Models\Approval::where('approvable_id', $unsuccessfull->id)
                    ->where('approvable_type', self::class)
                    ->update(['status' => 'archived']);

                // \App\Models\Pivots\UnsuccessfulTransactionToList::where('unsuccessful_transaction_id', $unsuccessfull->id)
                //     ->delete();
            });
        });


        // static::restored(function ($unsuccessfull) {
        //     // Restore related pivot records
        //     \App\Models\Pivots\UnsuccessfulTransactionToList::onlyTrashed()
        //         ->where('unsuccessful_transaction_id', $unsuccessfull->id)
        //         ->restore();
        // });
    }
}
