<?php

namespace App\Traits;

trait SelectCustomer
{
    function selectCustomer($id)
    {

        if ($this->context === 'orders') {
            $this->order->customer_id = $id;
            $this->customerSearch = $this->order->customer->name??'';
        } elseif ($this->context === 'sales') {

            $this->sale->customer_id = $id;
            $this->customerSearch = $this->sale->customer->name??'';
        }
   else{
            $this->dispatch('done', error: "Something Went Wrong: " );
        
    }
    }   
}
