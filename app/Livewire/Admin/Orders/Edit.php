<?php

namespace App\Livewire\Admin\Orders;

use App\Models\ActivityLog;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Traits\AddToList;
use App\Traits\DeleteCartItem;
use App\Traits\GetProductWithInventory;
use App\Traits\ProductSearch;
use App\Traits\SelectCustomer;
use App\Traits\SelectProduct;

use App\Traits\UpdatedProductSearch;
use App\Traits\WithCancel;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class Edit extends Component
{
    use ProductSearch;
    use DeleteCartItem;
    use WithCancel;
    use SelectCustomer;
    use UpdatedProductSearch;
    use SelectProduct;
    use AddToList;
    use GetProductWithInventory;

    protected string $context = 'orders';
    public $customerSearch;
    public $productSearch;
    public $selectedProductId;
    public $quantity;
    public $price;
    public Order $order;
    public $productList = [];
    public $overrideLowStock = false;
    public $pendingAction = null;
    public $productCache = [];

    function rules()
    {
        return [
            'quantity' => 'required|min:0.01|max:999999.99',
            'selectedProductId' => 'required',
            'price' => 'required|min:0.01|max:999999.99',
            'productList' => 'required'
                        ,'productList.*.quantity' => 'required|numeric|min:0.01|max:999999', // note: numeric → remove decimals
        ];
    }

    public  function mount($id)
    {
        // ✅ Load order with products
        $this->order = Order::with(['products', 'customer'])->findOrFail($id);

        // ✅ Build product list from pivot
        $this->productList = $this->order->products->map(fn($product) => [
            'product_id' => $product->id,
            'quantity'   => $product->pivot->quantity,
            'price'      => $product->pivot->unit_price,
        ])->toArray();

        // ✅ Cache product data (name only, inventory_balance loaded on demand)
        $this->cacheProductsFromOrder();

        // Pre-fill customer name in search box
        $this->customerSearch = $this->order->customer?->name ?? '';
    }

    // ✅ HELPER: Cache product names from loaded order
    private function cacheProductsFromOrder()
    {
        foreach ($this->order->products as $product) {
            $this->productCache[$product->id] = [
                'id' => $product->id,
                'name' => $product->name,
                'inventory_threshold' => $product->inventory_threshold ?? 10,
            ];
        }
    }

    // ✅ HELPER: Load products with inventory_balance





    private function resetForm()
    {
        $this->reset(['selectedProductId', 'productSearch', 'quantity', 'price']);
    }

    // ✅ Allow "continue anyway"
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


    private function logOrderActivity($oldProducts, $newProducts)
    {
        $changes = [
            'added'   => array_diff_key($newProducts, $oldProducts),
            'removed' => array_diff_key($oldProducts, $newProducts),
            'updated' => [],
        ];

        foreach ($newProducts as $id => $data) {
            if (isset($oldProducts[$id]) && $oldProducts[$id] != $data) {
                $changes['updated'][$id] = [
                    'old' => $oldProducts[$id],
                    'new' => $data,
                ];
            }
        }

        if (!empty($changes['added']) || !empty($changes['removed']) || !empty($changes['updated'])) {
            ActivityLog::create([
                'user_id'     => auth()->id(),
                'action'      => 'Updated',
                'model'       => 'Order',
                'changes'     => json_encode($changes),
                'ip_address'  => request()->ip(),
                'user_agent'  => request()->header('User-Agent'),
            ]);
        }
    }


    public function save()
    {
        $this->validateOnly('productList');
        $this->validateOnly('productList.*.quantity');

        try {



            $productIds  = array_column($this->productList, 'product_id');
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

                $newQty    = $listItem['quantity'];
                $threshold = $product['inventory_threshold'];

                if ($product['inventory_balance'] < $newQty) {
                    session()->flash(
                        'warning',
                        "Not enough stock for {$product['name']}. Available: {$product['inventory_balance']}."
                    );
                    return;
                }

                if (($product['inventory_balance'] - $newQty) < $threshold && !$this->overrideLowStock) {
                    $remaining = $product['inventory_balance'] - $newQty;
                    session()->flash(
                        'warning',
                        "Saving this will bring {$product['name']} below {$threshold}"
                    );
                    $this->pendingAction = ['type' => 'save', 'key' => $key];
                    return;
                }
            }

            $this->overrideLowStock = false;
            $this->pendingAction   = null;
            DB::beginTransaction();

            $oldProducts = $this->order->products()
                ->get()
                ->mapWithKeys(fn($p) => [
                    $p->id => [
                        'quantity'   => $p->pivot->quantity,
                        'unit_price' => $p->pivot->unit_price,
                    ]
                ])
                ->toArray();

            $newProducts = [];
            foreach ($this->productList as $listItem) {
                $newProducts[$listItem['product_id']] = [
                    'quantity'   => $listItem['quantity'],
                    'unit_price' => $listItem['price'],
                ];
            }

            $this->order->update();
            $this->order->products()->sync($newProducts);

            // ✅ LOG ACTIVITY (CLEAN CALL)
            $this->logOrderActivity($oldProducts, $newProducts);
            DB::commit();


            return redirect()
                ->route('admin.orders.index')
                ->with('success', 'Successfully Update.');
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

        $customers = Customer::select('id', 'name', 'tax_id')->where('name', 'like', '%' . $this->customerSearch . '%')->limit(10)->get();

        return view(
            'livewire.admin.orders.edit',
            [
                'customers' => $customers,
                'products' => $this->productSearch(),
                'productCache' => $this->productCache,
            ]
        );
    }
}
