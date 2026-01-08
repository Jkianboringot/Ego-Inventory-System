<?php

namespace App\Traits;

trait SelectSupplier
{
    
    function selectSupplier($id)
    {
        $this->purchase->supplier_id = $id;
        $this->supplierSearch = $this->purchase->supplier->name??'';
    }
}
