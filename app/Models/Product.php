<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;
    use SoftDeletes;

    // protected $appends = [
    //     'manual_url',
    // ];

    // Only append inventory_balance when explicitly loaded
    protected $with = [];

    // Relationships
    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function category()
    {
        return $this->belongsTo(ProductCategory::class, 'product_category_id');
    }

    public function sales()
    {
        return $this->belongsToMany(Sale::class, 'product_sale')
            ->withPivot(['quantity', 'unit_price']);
    }

    public function purchases()
    {
        return $this->belongsToMany(Purchase::class, 'product_purchase')
            ->withPivot(['quantity', 'unit_price']);
    }

    public function add_products()
    {
        return $this->belongsToMany(AddProduct::class, 'add_products_to_list')
            ->withPivot(['quantity']);
    }

    public function returns()
    {
        return $this->belongsToMany(ReturnItem::class, 'product_return')
            ->withPivot(['quantity', 'unit_price','adds_on']);
    }

    public function orders()
    {
        return $this->belongsToMany(Order::class, 'order_product')
            ->withPivot(['quantity', 'unit_price']);
    }

    public function unsuccessful_transactions()
    {
        return $this->belongsToMany(UnsuccessfulTransaction::class, 'unsuccessful_transactions_to_list')
            ->withPivot(['quantity']);
    }

    public function defecteds()
    {
        return $this->belongsToMany(Defected::class, 'defecteds_to_list')
            ->withPivot(['quantity','unit_price']);
    }


    public function getCachedInventoryBalance()
    {
        return Cache::remember(
            "product_{$this->id}_inventory",
            now()->addMinutes(5), // 5 minutes cache
            fn() => $this->calculateInventoryBalance()
        );
    }

    // Accessor for inventory_balance when it's in attributes (from SELECT query)
    public function getInventoryBalanceAttribute($value)
    {
        // If already calculated via SQL (from controller query), return it
        if (isset($this->attributes['inventory_balance'])) {
            return (float) $this->attributes['inventory_balance'];
        }

        // Otherwise calculate on-demand (fallback for single model loads)
        return $this->calculateInventoryBalance();
    }

    // Calculate inventory balance when not pre-loaded
    protected function calculateInventoryBalance()
    {
        return max(
            ($this->add_products()->where('status', 'approved')->sum('add_products_to_list.quantity') ?? 0)
                + ($this->returns()->where('status', 'approved')->sum('product_return.quantity') ?? 0)
                + ($this->unsuccessful_transactions()->where('status', 'approved')->sum('unsuccessful_transactions_to_list.quantity') ?? 0)
                - ($this->sales()->sum('product_sale.quantity') ?? 0)
                - ($this->orders()->sum('order_product.quantity') ?? 0)
                - ($this->defecteds()->sum('defecteds_to_list.quantity') ?? 0),
            0
        );
    }

    // Attribute accessors for when you need them individually
    //i really dont need this as of my undersntand as calcuateinventroy is doing the selection it self and calculataion this include the index
    // public function getTotalAddProductCountAttribute()
    // {
    //     return $this->add_products()
    //         ->where('status', 'approved')
    //         ->sum('add_products_to_list.quantity');
    // }

    // public function getTotalSalesCountAttribute()
    // {
    //     return $this->sales()->sum('product_sale.quantity');
    // }

    // public function getTotalOrdersCountAttribute()
    // {
    //     return $this->orders()->sum('order_product.quantity');
    // }
    // // public function getTotalDefectedsCountAttribute()
    // // {
    // //     return $this->defecteds()->sum('defecteds_to_list.quantity');
    // // }

    // public function getTotalReturnsCountAttribute()
    // {
    //     return $this->returns()
    //         ->where('status', 'approved')
    //         ->sum('product_return.quantity');
    // }



    // public function getTotalUnsuccessfulTransactionCountAttribute()
    // {
    //     return $this->unsuccessful_transactions()
    //         ->where('status', 'approved')
    //         ->sum('unsuccessful_transactions_to_list.quantity');
    // }


    // public function getManualUrlAttribute()
    // {
    //     if ($this->technical_path && file_exists(public_path($this->technical_path))) {
    //         return asset($this->technical_path);
    //     }
    //     return null;
    // }

    // protected function defaultProfilePhotoUrl()
    // {
    //     $name = trim(collect(explode(' ', $this->name))->map(function ($segment) {
    //         return mb_substr($segment, 0, 1);
    //     })->join(' '));

    //     return 'https://ui-avatars.com/api/?name=' . urlencode($name) . '&color=7F9CF5&background=EBF4FF';
    // }
}
