<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class ReturnItem extends Model
{
    use SoftDeletes, HasFactory;

    protected $fillable = ['return_type', 'reason', 'status'];

    public function editRequests()
    {
        return $this->morphMany(EditApproval::class, 'editable');
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_return')
            ->withPivot(['quantity', 'unit_price', 'adds_on']);
    }

    public function approvals()
    {
        return $this->morphMany(Approval::class, 'approvable');
    }

    /**
     * ✅ Optional: Only needed if you access returns individually (like show page)
     * The index already uses withSum(), so these won't be called there
     */
    public function getTotalQuantityAttribute($value)
    {
        // If already computed via withSum(), return it
        if (isset($this->attributes['total_quantity'])) {
            return (float) $this->attributes['total_quantity'];
        } //tis can be comment out since i dont compute this in index of return withsum

        // Fallback for single model loads
        if (!$this->relationLoaded('products')) {
            $this->load('products');
        }

        return $this->products->sum(fn($p) => $p->pivot->quantity);
    }

    public function getTotalAmountAttribute($value)
    {
        // If already in attributes (from SELECT subquery), return it
        if (isset($this->attributes['total_amount'])) {
            return (float) $this->attributes['total_amount'];
        }

        // Fallback: calculate on-demand for single model loads
        if (!$this->relationLoaded('products')) {
            $this->load('products');
        }

        return $this->products->sum(fn($p) => $p->pivot->quantity * $p->pivot->unit_price);
    }

    public function getAddsONAttribute($value)
    {
       
        // Fallback for single model loads
        if (!$this->relationLoaded('products')) {
            $this->load('products');
        }

        return $this->products->sum(fn($p) => $p->pivot->adds_on);
    }
    protected static function boot()
    {
        parent::boot();



        static::deleting(function ($return) {
            \App\Models\Approval::where('approvable_id', $return->id)
                ->where('approvable_type', self::class)
                ->update(['status' => 'archived']);
        });

        static::restored(function ($return) {
            \App\Models\Pivots\ProductReturn::onlyTrashed()
                ->where('return_item_id', $return->id)
                ->restore();

            \App\Models\ReturnItem::where('id', $return->id)
                ->update(['status' => 'pending']);
        });
    }
}
