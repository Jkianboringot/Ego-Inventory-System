<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Defected extends Model
{


    use SoftDeletes;
//  protected $appends = [
//     'defected_inventory_balance'
//     ];
//     // 'defected_total_amount',

    protected $fillable=['remarks'];
    function products()
    {
        return $this->belongsToMany(Product::class, 'defecteds_to_list')
            ->withPivot(['quantity','unit_price']);
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


//       public function getDefectedTotalAmountAttribute()
// {
//      if (!$this->relationLoaded('products')) {
//             $this->load('products');
//         }
//     return $this->products->sum(fn ($p) => $p->pivot->quantity * $p->pivot->unit_price);
// }
 



   
    // public function returnItem(){
    //     return $this->belongsToMany(ReturnItem::class);

    // } i dont need this ebucase defect does not related to return in anyway but i dont know


//     public function getDefectedInventoryBalanceAttribute()
// {
//      if (!$this->relationLoaded('products')) {
//             $this->load('products');
//         }

//         return $this->products->sum(fn($p) => $p->pivot->quantity);

// }

    protected static function booted() //ok will use booted since only attaching model event hooks (deleting, restored),
    //but if it was global like in sale ref its better to use parent::boot() to load thinkgs 
    //all the ref sale early and globally
    {



        static::restored(function ($defect) {
            // Restore related pivot records
            \App\Models\Pivots\DefectedToList::onlyTrashed()
                ->where('defecteds_id', $defect->id)
                ->restore();
        });
    }
}
