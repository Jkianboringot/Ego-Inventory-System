<?php

namespace App\Traits;

use App\Models\Product;

trait AddToList
{
    function addToList()
    {
        $this->validateOnly('selectedProductId');
        $this->validateOnly('price');
        $this->validateOnly('quantity');

        try {
            if (in_array($this->context, ['addproducts',  'unsuccessfull'])) {
               

                foreach ($this->productList as $key => $listItem) {
                    if ($listItem['product_id'] == $this->selectedProductId) {
                        $this->productList[$key]['quantity'] += $this->quantity;
                        return;
                    }
                }
                array_push($this->productList, [
                    'product_id' => $this->selectedProductId,
                    'quantity' => $this->quantity,

                ]);

                $this->updatedProductList();


                $this->reset([
                    'selectedProductId',
                    'productSearch',
                    'quantity',

                ]);
            } elseif (in_array($this->context, ['purchases','returns'])) {
               

                foreach ($this->productList as $key => $listItem) {
                    if ($listItem['product_id'] == $this->selectedProductId && $listItem['price'] == $this->price) {
                        $this->productList[$key]['quantity'] += $this->quantity;
                        $this->productList[$key]['price'] += $this->price;
                        $this->productList[$key]['adds_on'] += $this->adds_on;
                        return;
                        # code...gs
                        
                    }
                }


                array_push($this->productList, [
                    'product_id' => $this->selectedProductId,
                    'quantity' => $this->quantity,
                    'price' => $this->price,
                    'adds_on' => $this->adds_on,

                        

                ]);


                $this->updatedProductList();


                $this->reset([
                    'selectedProductId',
                    'productSearch',
                    'quantity',
                    'price',
                    'adds_on',
                ]);
            }
            
            
            elseif (in_array($this->context, ['sales', 'orders','defect'])) {
                 

            $products = $this->getProductsWithInventory([$this->selectedProductId]);
            $product = $products[$this->selectedProductId] ?? null;

            if (!$product) {
                $this->dispatch('done', error: "Warning: Product not found.");
                return;
            }

            $threshold = $product['inventory_threshold'];

            if ($product['inventory_balance'] < $this->quantity) {
                $this->dispatch('done', error: "Warning: Inventory balance for {$product['name']} is too low.");
                return;
            }

            if (($product['inventory_balance'] - $this->quantity) < $threshold && !$this->overrideLowStock) {
                $remaining=$product['inventory_balance'] - $this->quantity;
                session()->flash('warning', "Adding this will bring {$product['name']} below {$threshold} in stock. Remaining will be {$remaining}");
                $this->pendingAction = ['type' => 'addToList'];
                return;
            }

            // Check if product already in cart
            foreach ($this->productList as $key => $listItem) {
                if ($listItem['product_id'] == $this->selectedProductId && $listItem['price'] == $this->price) {
                    $this->productList[$key]['quantity'] += $this->quantity;
                    $this->overrideLowStock = false;
                    $this->resetForm();
                    return;
                }
            }

            // Add as new item
            array_push($this->productList, [
                'product_id' => $this->selectedProductId,
                'quantity' => $this->quantity,
                'price' => $this->price
            ]);

            $this->productCache[$this->selectedProductId] = $product;

            $this->resetForm();
            $this->overrideLowStock = false;
            }
        } catch (\Throwable $th) {
            $this->dispatch('done', error: "Something Went Wrong: " . $th->getMessage());
        }
    }
}
