<?php

namespace App\Livewire\Admin\Defected;

use App\Models\Defected;
use App\Models\Product;
use App\Traits\AddToList;
use App\Traits\GetProductWithInventory;
use App\Traits\UpdatedProductSearch;
use Livewire\Component;
use App\Traits\AddToQuantity;
use App\Traits\DeleteCartItem;
use App\Traits\ProductSearch;
use App\Traits\SelectProduct;
use App\Traits\SubToQuantity;
use App\Traits\WithCancel;
use Illuminate\Support\Facades\DB;

class Edit extends Component
{
    use ProductSearch;
    use WithCancel;
    use SubToQuantity;
    use AddToQuantity;
    use DeleteCartItem;
    use SelectProduct;
    use UpdatedProductSearch;
    use AddToList;
    use GetProductWithInventory;

    public $productSearch;
    protected string $context = 'defect';

    public $overrideLowStock = false;
    public $pendingAction = null;
    public $productCache = [];

    public $selectedProductId;

    public $quantity;
    public $price;


    public Defected $defected;
    public $productList = [];


    function rules()
    {
        return [
            'defected.remarks' => 'nullable|max:255',
            'quantity' => 'required|min:0.01|max:999999.99',
            'selectedProductId' => 'required',
            'productList' => 'required',
            'price' => 'required|min:0.01|max:999999.99',

            'productList.*.quantity' => 'required|numeric|min:0.01|max:999999', // note: numeric → remove decimals
        ];
    }



    function mount($id)
    {
        $this->defected = Defected::findOrFail($id);
        //got rid of eager loading with unit
        $this->productList = $this->defected->products->map(fn($product) => [
            'product_id' => $product->id,
            'quantity' => $product->pivot->quantity,
            'price' => $product->pivot->unit_price,
        ])->toArray();
    }



    function selectProduct($id)
    {
        $this->selectedProductId = $id;
        $this->productSearch = Product::find($id)->name;
    }
    // function addToList()
    // {
    //     $this->validate();
    //     try {
    //         // if (empty($this->defected->unsuccessful_transactions_date)) {
    //         //     $this->defected->unsuccessful_transactions_date = now()->toDateString();
    //         // }           // Manual checks to replace validate()
    //         // if (!$this->selectedProductId || !$this->quantity) {
    //         //     throw new \Exception('All product fields are required.');
    //         // }

    //         foreach ($this->productList as $key => $item) {
    //             if ($item['product_id'] == $this->selectedProductId) {
    //                 $this->productList[$key]['quantity'] += $this->quantity;
    //                 $this->reset(['selectedProductId', 'productSearch', 'quantity']);
    //                 return;
    //             }
    //         }

    //         // Add new item to list
    //         $this->productList[] = [
    //             'product_id' => $this->selectedProductId,
    //             'quantity' => $this->quantity,
    //         ];

    //         // Clean up input fields
    //         $this->reset(['selectedProductId', 'productSearch', 'quantity']);
    //     } catch (\Throwable $th) {
    //         $this->dispatch('done', error: "Something went wrong: " . $th->getMessage());
    //     }

    // }
    public function continueAnyway()
    {
        $this->overrideLowStock = true;

        if ($this->pendingAction) {
            if ($this->pendingAction['type'] === 'addToList') {
                $this->addToList();
            } elseif ($this->pendingAction['type'] === 'save') {
                $this->save();
            } else {
                return;
            }
        }

        $this->pendingAction = null;
    }

    private function resetForm()
    {
        $this->reset(['selectedProductId', 'productSearch', 'quantity', 'price']);
    }
    function save()
    {
        $this->validateOnly('defected.remarks');
        $this->validateOnly('productList');
        $this->validateOnly('productList.*.quantity');

        try {
            $productIds = array_column($this->productList, 'product_id');
            $allProducts = $this->getProductsWithInventory($productIds);

            foreach ($this->productList as $key => $listItem) {
                $product = $allProducts[$listItem['product_id']] ?? null;
                if (!$product) {
                    throw new \Exception("Product not found.");
                }

                if ($listItem['quantity'] <= 0) {
                    $this->dispatch('done', error: "Quantity cannot be zero");
                    return;
                }

                $newQty = $listItem['quantity'];
                $threshold = $product['inventory_threshold'];

                if ($product['inventory_balance'] < $newQty) {
                    session()->flash('warning', "Not enough stock for {$product['name']}. Available: {$product['inventory_balance']}.");
                    return;
                }

                if (($product['inventory_balance'] - $newQty) < $threshold && !$this->overrideLowStock) {
                    $remaining = $product['inventory_balance'] - $this->quantity;

                    session()->flash('warning', "Saving this will bring {$product['name']} below Threshold:{$threshold} in stock");
                    $this->pendingAction = ['type' => 'save', 'key' => $key];
                    return;
                }
            }

            $this->overrideLowStock = false;
            $this->pendingAction = null;



            DB::beginTransaction();
            $this->defected->save();

           $newProducts = [];
            foreach ($this->productList as $listItem) {
                $newProducts[$listItem['product_id']] = [
                    'quantity'   => $listItem['quantity'],
                    'unit_price' => $listItem['price'],
                ];
            }

            $this->defected->products()->sync($newProducts);

            DB::commit();


            return to_route('admin.defected.index')
                ->with('success', 'Successfully Updated.');
        } catch (\Throwable $th) {
            if (DB::transactionLevel() > 0) {
                DB::rollBack();
            }
            $this->dispatch('done', error: "Something Went Wrong: " . $th->getMessage());
        }
    }

    public function render()
    {
        $productsToLoad = array_filter(
            array_column($this->productList, 'product_id'),
            fn($id) => !isset($this->productCache[$id])
        );

        if (!empty($productsToLoad)) {
            $newProducts = $this->getProductsWithInventory($productsToLoad);
            $this->productCache = array_merge($this->productCache, $newProducts);
        }


        return view(
            'livewire.admin.defected.edit',
            [
                'products' => $this->productSearch(),
                'productCache' => $this->productCache,
            ]
        );
    }
}
