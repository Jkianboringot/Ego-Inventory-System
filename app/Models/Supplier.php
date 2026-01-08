<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Supplier extends Model
{
    use SoftDeletes;
        use HasFactory;
    protected $fillable = ['name','phone','address','account_number','tax_id'];
    
    function purchases()
    {
        return $this->hasMany(Purchase::class);
    }


    function products()
    {
        return $this->hasMany(Product::class);
    }

//⚠️ would be great to be able to use this but i cannot beucase i cant use pagination in this case and
// all the normal function like search and manual calculation will not work
// but maybe in the future i can use this
//  protected static function boot()
//     {
//         parent::boot();
        
//         static::saved(function () {
//             Cache::forget('suppliers_list');

//             static::deleted(fn() => Cache::forget('suppliers_list'));

//         });
//     }
    
//     public static function getCached()
//     {
//         return Cache::remember('suppliers_list', 3600, function () {
//             return static::all();
//         });
//     }



    //     public function getTotalAmountAttribute()
    // {
    //     return $this->purchases()->get()->sum(function ($purchase) {
    //         return $purchase->total_amount;
    //     });
    // }


    //    function getTotalBalanceAttribute()
    // {
    //         return $this->total_amount - $this->total_paid; 
    // }

}
