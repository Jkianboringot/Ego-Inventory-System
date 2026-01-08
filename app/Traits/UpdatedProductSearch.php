<?php

namespace App\Traits;

use App\Models\Product;

trait UpdatedProductSearch
{
    // this is for auto adding with barcode in create

    
    public function updatedProductSearch($value)
    //⚠️⚠️⚠️this is a nice funciton:
        // ok so hw it works is when a property is change , 
        // and you made a fucntion with updated(only workd beucase of this, 
        // beucase it detect a change in property )<propername> ,is called
        //  becuse this function is called everytime a property is change or updated
    {
        $value = trim($value);
        $product = Product::where('barcode', $value)->first();
        if (!$product) return;

        if(!$this->quantity){
            $this->quantity=1;
        }

        if(in_array($this->context,['sales','orders','returns'])){
            $this->price=$product->sale_price;
        }
        
        if($this->context=== 'purchases'){
            $this->price=$product->purchase_price;
        }
        // this will not work i need to figyre out hwo can i make this funciton know which class its being called ,example 
        // purhcase sale or non of does

        $this->selectedProductId = $product->id;
        $this->addToList(); // already handles auto-increment
        $this->productSearch = ''; // ready for next scan
    }
}
