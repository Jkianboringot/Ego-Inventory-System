<?php

// ============================================
// Purchase Model
// ============================================

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Purchase extends Model
{
    use SoftDeletes, HasFactory;

    protected $guarded = ['id'];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_purchase')
            ->withPivot(['quantity', 'unit_price']);
    }

    /**
     * ✅ Smart accessor: uses pre-computed value if available
     */
    public function getTotalAmountAttribute($value)
    {
        // If already in attributes (from SELECT subquery), return it
        if (isset($this->attributes['total_amount'])) {
            return (float) $this->attributes['total_amount'];
        }

        // Fallback: calculate on-demand
        if (!$this->relationLoaded('products')) {
            $this->load('products');
        }

        return $this->products->sum(fn($p) => $p->pivot->quantity * $p->pivot->unit_price);
    }

    public function getTotalQuantityAttribute($value)
    {
        if (isset($this->attributes['total_quantity'])) {
            return (float) $this->attributes['total_quantity'];
        }

        if (!$this->relationLoaded('products')) {
            $this->load('products');
        }

        return $this->products->sum(fn($p) => $p->pivot->quantity);
    }

    public function getTotalValueAttribute()
    {
        if (!$this->relationLoaded('products')) {
            $this->load('products');
        }

        return $this->products->sum(fn($p) => $p->pivot->quantity * $p->purchase_price);
    }

    protected static function booted()
    {
        static::restored(function ($purchase) {
            \App\Models\Pivots\ProductPurchase::onlyTrashed()
                ->where('purchase_id', $purchase->id)
                ->restore();
        });
    }
}