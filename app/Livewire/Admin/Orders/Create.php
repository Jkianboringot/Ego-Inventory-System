<?php

namespace App\Livewire\Admin\Orders;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Order;
use App\Traits\AddToList;
use App\Traits\DeleteCartItem;
use App\Traits\GetProductWithInventory;
use App\Traits\ProductSearch;
use App\Traits\SelectCustomer;
use App\Traits\SelectProduct;
use App\Traits\UpdatedProductSearch;
use App\Traits\WithCancel;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class Create extends Component
{
    use ProductSearch,
        WithCancel,
        DeleteCartItem,
        SelectCustomer,
        UpdatedProductSearch,
        SelectProduct,
        AddToList,
        GetProductWithInventory;

    protected string $context = 'orders';
    public $customerSearch;
    public $productSearch;
    public $selectedProductId;
    public $quantity;
    public $price;
    public $overrideLowStock = false;
    public $pendingAction = null;
    public Order $order;
    public $productList = [];
    public $productCache = [];

    function rules()
    {
        return [
            'quantity' => 'required|min:0.01|max:999999.99',
            'selectedProductId' => 'required',
            'price' => 'required|min:0.01|max:999999.99',
            'productList' => 'required',
            'productList.*.quantity' => 'required|numeric|min:0.01|max:999999', // note: numeric → remove decimals
        ];
    }

    public function mount()
    {
        $this->order = new Order();
    }




    private function resetForm()
    {
        $this->reset(['selectedProductId', 'productSearch', 'quantity', 'price']);
    }

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

    public function save()
    {
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
                $threshold = $product['inventory_threshold'] ?? 10;

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

            $this->order->save();

            foreach ($this->productList as $listItem) {
                $this->order->products()->attach($listItem['product_id'], [
                    'quantity' => $listItem['quantity'],
                    'unit_price' => $listItem['price']
                ]);
            }
            DB::commit();

            return redirect()->route('admin.orders.index')
                ->with('success', 'Successfully Created.');
        } catch (\Throwable $th) {
            if (DB::transactionLevel() > 0) {
                DB::rollBack();
            }
            $this->dispatch('done', error: "Something Went Wrong: " . $th->getMessage());
        }
    }

    public function render()
    {
        // ✅ Load any missing products from cache
        $productsToLoad = array_filter(
            array_column($this->productList, 'product_id'),
            fn($id) => !isset($this->productCache[$id])
        );

        if (!empty($productsToLoad)) {
            $newProducts = $this->getProductsWithInventory($productsToLoad);
            $this->productCache = array_merge($this->productCache, $newProducts);
        }

        $customers = Customer::select('id', 'name', 'tax_id')->where('name', 'like', '%' . $this->customerSearch . '%')
            ->orWhere('tax_id', 'like', '%' . $this->customerSearch . '%')->limit(10)->get();

        return view(
            'livewire.admin.orders.create',
            [
                'customers' => $customers,
                'products' => $this->productSearch(),
                'productCache' => $this->productCache,
            ]
        );
    }
}
