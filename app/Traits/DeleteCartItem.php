<?php

namespace App\Traits;

trait DeleteCartItem
{
        function deleteCartItem($key)
    {
        array_splice($this->productList, $key, 1);
    }
}
