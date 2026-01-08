<?php

namespace App\Traits;

trait SubToQuantity
{
      function subtractQuantity($key)
    {
        if ($this->productList[$key]['quantity'] > 1) {
            $this->productList[$key]['quantity']--;
        }
    }

}
