<?php

namespace App\Traits;

use App\Models\Product;

trait LoadedProducts
{
    public $loadedProducts = [];

    public function updatedProductList()
    {
        $ids = collect($this->productList)->pluck('product_id')->unique();
        $this->loadedProducts = Product::whereIn('id', $ids)->get();
                                        //with('unit')->
    }

}
