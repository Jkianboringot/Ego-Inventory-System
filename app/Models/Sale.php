<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class Sale extends Model
{
    use SoftDeletes, HasFactory;

    protected $fillable = ['sales_ref'];


    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_sale')
            ->withPivot(['quantity', 'unit_price']);
    }

    public function getTotalAmountAttribute($value)
    {
        if (isset($this->attributes['total_amount'])) {
            return (float) $this->attributes['total_amount'];
        }

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
        if (!$this->exists) return 0;

        if (!$this->relationLoaded('products')) {
            $this->load('products');
        }

        return $this->products->sum(fn($p) => $p->pivot->quantity * $p->purchase_price);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($sale) {
            do {
                $ref = 'Sal-' . now()->format('YmdHis') . '-' . Str::upper(Str::random(3));
            } while (self::where('sales_ref', $ref)->exists());

            $sale->sales_ref = $ref;
        });

        static::restored(function ($sale) {
            \App\Models\Pivots\ProductSale::onlyTrashed()
                ->where('sale_id', $sale->id)
                ->restore();
        });
        // static::updated(function ($sale) {
        //     // Only trigger if sale_status changed to true
        //     if ($sale->return_status && $sale->wasChanged('return_status')) {
        //         // Create UnsuccessfulTransaction
        //         $ut = ReturnItem::create([
        //             'status' => 'pending',
        //             'sale_id' => $sale->id,
        //         ]);

        //         // Attach products from the sale
        //         foreach ($sale->products as $product) {
        //             $ut->products()->attach($product->id, [
        //                 'quantity' => $product->pivot->quantity,
        //                 'unit_price' => $product->pivot->unit_price,

        //             ]);
        //         }
        //     }
        // });
    }
}
