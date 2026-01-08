<?php

namespace App\Livewire\Admin\Defected;

use App\Models\Defected;
use App\Models\Product;
use App\Traits\AddToList;
use App\Traits\AddToQuantity;
use App\Traits\DeleteCartItem;
use App\Traits\GetProductWithInventory;
use App\Traits\ProductSearch;
use App\Traits\SelectProduct;
use App\Traits\SubToQuantity;
use App\Traits\UpdatedProductSearch;
use App\Traits\WithCancel;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Create extends Component
{
    use ProductSearch;
    use WithCancel;
    use SubToQuantity;
    use UpdatedProductSearch;

    use AddToQuantity;
    use DeleteCartItem;
    use SelectProduct;
    use AddToList;
    use GetProductWithInventory;
    public $productSearch;

    public $selectedProductId;

    public $quantity;

    public $pendingAction = null;

    public $price;


    public $overrideLowStock = false;

    protected string $context = 'defect';

    public Defected $defected;


    public $productList = [];

    public $productCache = [];

    public $loadedProducts = [];

    function rules()
    {
        return [
            'defected.remarks' => 'nullable|max:255',
            'quantity' => 'required|min:0.01|max:999999.99',
            'selectedProductId' => 'required',
            'price' => 'required|min:0.01|max:999999.99',

            'productList' => 'required',
            'productList.*.quantity' => 'required|numeric|min:0.01|max:999999', // note: numeric → remove decimals

        ];
    }



    function mount()
    {
        $this->defected = new Defected();
    }





    //    public function updated($propertyName)
    // {    try using this in more thatn the jsut validate this is better becuae its more focus and dont rerun all rules like validate with everykey stroke 
    //     $this->validateOnly($propertyName);
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

                if ($listItem['quantity'] <= 0) {
                    $this->dispatch('done', error: "Quantity cannot be zero");
                    return;
                }

                if (!$product) {
                    throw new \Exception("Product not found.");
                }

                $newQty = $listItem['quantity'];
                $threshold = $product['inventory_threshold'];

                if ($product['inventory_balance'] < $newQty) {
                    session()->flash('warning', "Not enough stock for {$product['name']}. Available: {$product['inventory_balance']}.");
                    return;
                }

                if (($product['inventory_balance'] - $newQty) < $threshold && !$this->overrideLowStock) {
                    $remaining = $product['inventory_balance'] - $this->quantity;
                    session()->flash('warning', "Saving this will bring {$product['name']} below {$threshold} in stock");
                    $this->pendingAction = ['type' => 'save', 'key' => $key];
                    return;
                }
            }

            $this->overrideLowStock = false;
            $this->pendingAction = null;




            DB::beginTransaction();


            $this->defected->save();


            foreach ($this->productList as $listItem) {
                $this->defected->products()->syncWithoutDetaching([
                    $listItem['product_id'] => [
                        'quantity' => $listItem['quantity'],
                        'unit_price' => $listItem['price']

                    ],
                ]);



                //make this a function
                \App\Models\ActivityLog::create([
                    'user_id' => auth()->id(),
                    'action' => 'stock_added',
                    'model' => 'Product',
                    'changes' => json_encode([
                        'added_quantity' => $listItem['quantity'],
                    ]),
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->header('User-Agent'),
                ]);
            };

            DB::commit();
            return to_route('admin.defected.index')->with('success', 'Successfully Created.');
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
            'livewire.admin.defected.create',
            [
                'products' => $this->productSearch(),
                'productCache' => $this->productCache,
            ]
        );
    }
}
