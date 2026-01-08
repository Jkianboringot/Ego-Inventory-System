<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class Order extends Model
{
    use SoftDeletes, HasFactory;

    protected $fillable = ['order_status', 'orders_ref'];

    // Remove from appends - computed on-demand
    // protected $appends = ['total_amount'];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'order_product')
            ->withPivot(['quantity', 'unit_price']);
    }

    // i dont even need this for now, so i can remove it later
    public function unsuccessfulTransactions()
    {
        return $this->hasMany(UnsuccessfulTransaction::class);
    }



    /**
     * ✅ Smart accessor: uses pre-computed value if available, calculates otherwise
     */
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

    protected static function boot()
    {
        parent::boot();


        static::creating(function ($order) {
            do {
                $ref = 'Ord-' . now()->format('YmdHis') . '-' . Str::upper(Str::random(3));
            } while (self::where('orders_ref', $ref)->exists());

            $order->orders_ref = $ref;
        });

        static::restored(function ($order) {
            \App\Models\Pivots\OrderProduct::onlyTrashed()
                ->where('order_id', $order->id)
                ->restore();
        });

        // static::updated(function ($order) {
        //     // Only trigger if order_status changed to true
        //     if ($order->order_status && $order->wasChanged('order_status')) {
        //         // Create UnsuccessfulTransaction
        //         $ut = UnsuccessfulTransaction::create([
        //             'status' => 'pending',
        //             'order_id' => $order->id,
        //         ]);
            // this is for automatically create a record in unsuccessfull when order status change
        //         // Attach products from the order
        //         foreach ($order->products as $product) {
        //             $ut->products()->attach($product->id, [
        //                 'quantity' => $product->pivot->quantity,
        //             ]);
        //         }
        //     }
            // } elseif ($order->return_status && $order->wasChanged('return_status')) {
            //     $ut = ReturnItem::create([
            //         'status' => 'pending',
            //         'order_id' => $order->id,
            //     ]);

            //     // Attach products from the order
            //     foreach ($order->products as $product) {
            //         $ut->products()->attach($product->id, [
            //             'quantity' => $product->pivot->quantity,
            //             'unit_price' => $product->pivot->unit_price,
            //         ]);
            //     }
            // }
        // });
    }
}
