<?php

namespace App\Livewire\Admin\Sales;

use App\Models\ActivityLog;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use App\Traits\AddToList;
use App\Traits\DeleteCartItem;
use App\Traits\GetProductWithInventory;
use App\Traits\ProductSearch;
use App\Traits\SelectCustomer;
use App\Traits\SelectProduct;
use App\Traits\SubToQuantity;
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
    use UpdatedProductSearch;
    use WithCancel;
    use DeleteCartItem;
    use SelectCustomer;
    use SelectProduct;
    use AddToList;
    use GetProductWithInventory;


    public $overrideLowStock = false;
    public $customerSearch;
    public $productSearch;
    public $selectedProductId;
    public $quantity;
    public $price;
    public $pendingAction = null;
    protected string $context = 'sales';
    public Sale $sale;
    public $productList = [];
    public $productCache = [];

    function rules()
    {
        return [
            'quantity' => 'required|min:0.01|max:999999.99',
            'selectedProductId' => 'required',
            'price' => 'required|min:0.01|max:999999.99',
            'productList' => 'required',
            'productList.*.quantity' => 'required|integer|min:0.01|max:999999', // note: integer → remove decimals
        ];
    }

    function mount($id)
    {
        // ✅ Load sale with products and customer
        $this->sale = Sale::with(['products', 'customer'])->findOrFail($id);

        $this->productList = $this->sale->products->map(fn($p) => [
            'product_id' => $p->id,
            'quantity'   => $p->pivot->quantity,
            'price'      => $p->pivot->unit_price,
        ])->toArray();

        $this->cacheProductsFromSale();

        $this->customerSearch = $this->sale->customer?->name ?? '';
    }


    private function cacheProductsFromSale()
    {
        foreach ($this->sale->products as $product) {
            $this->productCache[$product->id] = [
                'id' => $product->id,
                'name' => $product->name,
                'inventory_threshold' => $product->inventory_threshold ?? 10,
            ];
        }
    }




    function selectProduct($id)
    {
        $this->selectedProductId = $id;

        $product = Product::find($id);

        if ($product) {
            $this->productSearch = $product->name;
            $this->price = $product->sale_price;
        }
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
                'user_id'    => auth()->id(),
                'action'     => 'Updated',
                'model'      => 'Sale',
                'changes'    => json_encode($changes),
                'ip_address' => request()->ip(),
                'user_agent' => request()->header('User-Agent'),
            ]);
        }
    }


    function save()
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


            $oldProducts = $this->sale->products()
                ->get()
                ->mapWithKeys(fn($p) => [
                    $p->id => [
                        'quantity'   => $p->pivot->quantity,
                        'unit_price' => $p->pivot->unit_price,
                    ]
                ])
                ->toArray();

            $newProducts = [];
            foreach ($this->productList as $item) {
                $newProducts[$item['product_id']] = [
                    'quantity'   => $item['quantity'],
                    'unit_price' => $item['price'],
                ];
            }

            $this->sale->products()->sync($newProducts);

            $this->sale->update();

            $this->logOrderActivity($oldProducts, $newProducts);

            DB::commit();

            return redirect()->route('admin.sales.index')
                ->with('success', 'Successfully Updated.');
        } catch (\Throwable $th) {
            if (DB::transactionLevel() > 0) {
                DB::rollBack();
            }
            $this->dispatch('done', error: "Error: " . $th->getMessage());
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

        $customers = Customer::select('id', 'name', 'tax_id')->where('name', 'like', '%' . $this->customerSearch . '%')
            ->orWhere('tax_id', 'like', '%' . $this->customerSearch . '%')->limit(10)->get();

        return view(
            'livewire.admin.sales.edit',
            [
                'customers' => $customers,
                'products' => $this->productSearch(),
                'productCache' => $this->productCache,
            ]
        );
    }
}
