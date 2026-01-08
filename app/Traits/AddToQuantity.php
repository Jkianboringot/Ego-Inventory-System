<?php

namespace App\Traits;

trait AddToQuantity
{
       function addQuantity($key)
    {
        $this->productList[$key]['quantity']++;
    }
}
