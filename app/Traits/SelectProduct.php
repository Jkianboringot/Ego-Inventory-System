<?php

namespace App\Traits;

use App\Models\Product;

trait SelectProduct
{
      function selectProduct($id)
    {
        $this->selectedProductId = $id;
        $product = Product::find($id);

        if ($product) {
            $this->productSearch = $product->name;
            $this->price = $product->sale_price; 
                //if i got rid of this it fixis unsuccessfull, but if i did not other wont break
                //ok unsuccessfull does work but i need to be carefull beucase it might be adding 
                //price to unsucessfull pivot which i dont want
        }
    }
}
