<?php

namespace App\Models;

use App\Livewire\Admin\Approvals\AddApproval;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class AddProduct extends Model
{
   
    use SoftDeletes;

   use HasFactory;

      protected $fillable = ['status'];

    
public function approvals()
{
    return $this->morphMany(Approval::class, 'approvable');
}

public function editRequests()
{
    return $this->morphMany(EditApproval::class, 'editable');
}
public function products()
{
    return $this->belongsToMany(Product::class, 'add_products_to_list')
        ->using(\App\Models\Pivots\AddProductToList::class)
        ->withPivot(['quantity']);
        // i might not even need this pivot models i just added them for easier access in terminal, but they
        // can be use if i want specific conditon or validation, and i need this beucase my pivots have condition
        //for archiving , and restoring for approval so all what is envolve with apprival must have this
}

  public function getTotalQuantityAttribute()
    {
          if (!$this->relationLoaded('products')) {
            $this->load('products');
        }

    return $this->products->sum(fn($p) => $p->pivot->quantity);
    }
    //⚠️⚠️⚠️for some reason this is broken in reutrn but in here its fine investigate, learn you system fuck deadline

  
    //commented beucae of optimize and this total quantity cna just be access in index now , more scallable
    //    public function getTotalQuantityAttribute()
    // {
    //     return $this->products()->get()->sum(function ($product) {
    //         return $product->pivot->quantity ;
    //     });
    // }
    protected static function booted()
    //ok will use booted since only attaching model event hooks (deleting, restored),
                                            //but if it was global like in sale ref its better to use parent::boot() to load thinkgs 
                                            //all the ref sale early and globally
{
    //for delting a record it automatically set approval to archived
        //fix this and add soft delete in the future becuase this does not do anything 
        //i think 
    static::deleting(function ($addProduct) {
        \App\Models\Approval::where('approvable_id', $addProduct->id)
            ->where('approvable_type', self::class)
            ->update(['status' => 'archived']);

    });

    //this is for restoring, but only for system admin only is if something need
    //to be restore i can just do "AddProduct::onlyTrashed()->find(1)->restore()" and it will restore the 
    //record along with pivot tables record and automatically set approval to 
    //pending to be safe , but you ahve to make sure to pass in a id beucae if not it will not 
    //restore the AddProductToList becuase it does not know what idea you mean 
    //and it will only restore the addproduct not the AddProductToList

    // if you incounter bug with restoring, use find not where when restoring 
    
    //   Use below for debugging  
    //  App\Models\Sale::onlyTrashed()->get();   
    // App\Models\Sale::withTrashed()->find(20)->restore()  
    // App\Models\Pivots\ProductSale::onlyTrashed()->get()
      static::restored(function ($addProduct) {
        // Restore related pivot records
        \App\Models\Pivots\AddProductToList::onlyTrashed()
            ->where('add_product_id', $addProduct->id)
            ->restore();

            //this is just to set the status back to pending so that it dont add
            //all the previus quantity , its much better for all those to only comeback
            //onces approve again , to double-double check
        \App\Models\AddProduct::where('id', $addProduct->id)
            ->update(['status' => 'pending']);
    });
}


}
