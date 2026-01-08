<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Customer extends Model
{
    use SoftDeletes;
use HasFactory;
    protected $fillable = ['name','phone','address','organization_type','tax_id'];

       function orders(){
        return $this->hasMany(Order::class);
    }

     function products()
    {
        return $this->belongsToMany(Product::class, 'product_sale')->withPivot(['quantity', 'unit_price']);
    }
    
         function sales(){
        return $this->hasMany(Sale::class);
    }

   

    //     public function getTotalAmountAttribute()
    // {
    //     return $this->sales()->get()->sum(function ($sale) {
    //         return $sale->total_amount;
    //     });
    // }

    //    function getTotalBalanceAttribute()
    // {
    //         return $this->total_amount - $this->total_paid; 
    // }

    
}
