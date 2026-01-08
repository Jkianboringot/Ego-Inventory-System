<?php

namespace App\Traits;

trait AccountingRules
{
    
    function rules()
    {
        return [
            'quantity' => 'required|integer|min:1|max:999999',
            'selectedProductId' => 'required',
            'productList' => 'required'


        ];
    }
}
