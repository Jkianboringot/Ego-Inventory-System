<?php

namespace App\Livewire\Admin\Purchases;

use App\Models\Product;
use App\Models\Purchase;
use App\Models\Supplier;
use App\Traits\AddToQuantity;
use App\Traits\DeleteCartItem;
use App\Traits\ProductSearch;
use App\Traits\SelectProduct;
use App\Traits\SelectSupplier;
use App\Traits\SubToQuantity;
use App\Traits\UpdatedProductSearch;
use App\Traits\WithCancel;
use Livewire\Component;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class Edit extends Component
{
    use ProductSearch;
    use WithCancel;
    use UpdatedProductSearch;

    use DeleteCartItem;
    use SelectSupplier;

    public $supplierSearch;
    protected string $context = 'purchases';
    public $productSearch;

    public $selectedProductId;

    public $quantity;
    public $price;


    public Purchase $purchase;
    public $productList = [];
    public $productCache = [];


    function rules()
    {
        return [
            'purchase.date_settled' => 'required|date',
            'purchase.is_paid' => 'required',
            'quantity' => 'required|min:0.01|max:999999.99',
            'selectedProductId' => 'required',
            'price' => 'required|min:0.01|max:999999.99',
            'productList' => 'required'
                        ,'productList.*.quantity' => 'required|numeric|min:0.01|max:999999', // note: numeric → remove decimals
        ];
    }



    //     function mount($id)
    //     {
    //          $this->purchase = Purchase::with('products.unit')->findOrFail($id);

    //         $this->productList = $this->purchase->products->map(fn($product) => [
    //     'product_id' => $product->id,
    //     'quantity' => $product->pivot->quantity,
    //     'price' => $product->pivot->price ?? null,
    // ])->toArray();

    //         $this->supplierSearch = $this->purchase->supplier->name;
    //     }


    function mount($id)
    {
        // Load the order along with its products and customer relationship
        $this->purchase = purchase::with(['products', 'supplier'])->findOrFail($id);
        $this->productCache = $this->purchase->products->keyBy('id')->toArray();

        // Initialize the product list for display
        $this->productList = $this->purchase->products->map(fn($product) => [
            'product_id' => $product->id,
            'name'   => $product->name,
            'quantity'   => $product->pivot->quantity,
            'price'      => $product->pivot->unit_price,
        ])->toArray();

        // Pre-fill customer name in search box
        $this->supplierSearch = $this->purchase->supplier?->name ?? '';
    }


    function selectProduct($id)
    {
        $this->selectedProductId = $id;
        $product = Product::find($id);

        if ($product) {
            $this->productSearch = $product->name;
            $this->price = $product->purchase_price;
            //if i got rid of this it fixis unsuccessfull, but if i did not other wont break
            //ok unsuccessfull does work but i need to be carefull beucase it might be adding 
            //price to unsucessfull pivot which i dont want
        }
    }



    function addToList()
    {
        $this->validateOnly('price');

        $this->validateOnly('quantity');
        $this->validateOnly('selectedProductId');


        try {
            if (!$this->selectedProductId || !$this->quantity || !$this->price) {
                throw new \Exception('All product fields are required.');
            }

            foreach ($this->productList as $key => $item) {
                if ($item['product_id'] == $this->selectedProductId && $item['price'] == $this->price) {
                    $this->productList[$key]['quantity'] += $this->quantity;
                    $this->reset(['selectedProductId', 'productSearch', 'quantity', 'price']);
                    return;
                }
            }

            $this->productList[] = [
                'product_id' => $this->selectedProductId,
                'quantity' => $this->quantity,
                'price' => $this->price
            ];

            // Clean up input fields
            $this->reset(['selectedProductId', 'productSearch', 'quantity', 'price']);
        } catch (\Throwable $th) {
            $this->dispatch('done', error: "Something went wrong: " . $th->getMessage());
        }
    }
    function save()
    {
        $this->validateOnly('purchase.is_paid');
        $this->validateOnly('purchase.date_settled');
        $this->validateOnly('productList');
        $this->validateOnly('productList.*.quantity');

        try {


            foreach ($this->productList as $listItem) {

                if ($listItem['quantity'] <= 0) {
                    $this->dispatch('done', error: "Quantity cannot be zero");
                    return;
                }
            }
            DB::beginTransaction();
            $this->purchase->update();

            $oldProducts = $this->purchase->products()
                ->get()
                ->mapWithKeys(function ($p) {
                    return [$p->id => [
                        'quantity'   => $p->pivot->quantity,
                        'unit_price' => $p->pivot->unit_price,
                    ]];
                })
                ->toArray();

            $newProducts = [];
            foreach ($this->productList as $listItem) {
                $newProducts[$listItem['product_id']] = [
                    'quantity'   => $listItem['quantity'],
                    'unit_price' => $listItem['price'],
                ];
            }

            $this->purchase->products()->sync($newProducts);

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
                    'user_id'   => auth()->id(),
                    'action'    => 'Updated',
                    'model'     => 'Purchase',
                    'model_id'  => $this->purchase->id,
                    'changes'   => json_encode($changes),
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->header('User-Agent'),
                ]);
            }
            DB::commit();

            return redirect()->route('admin.purchases.index')
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
        $loadedProducts = Product::whereIn('id', collect($this->productList)->pluck('product_id'))->get();

        $suppliers = Supplier::select('id', 'name', 'tax_id')
            ->where('name', 'like', '%' . $this->supplierSearch . '%')
            ->orWhere('tax_id', 'like', '%' . $this->supplierSearch . '%')

            ->limit(10)->get();

        $paidOptions = [
            'Paid',
            'Unpaid',
            'Partially_Paid',
        ];
        return view(
            'livewire.admin.purchases.edit',
            [
                'suppliers' => $suppliers,
                'products' => $this->productSearch(),

                'paidOptions' => $paidOptions,
                'loadedProducts' => $loadedProducts,



            ]
        );
    }
}

// if something brike look at make purchase funstion i change it from atteach to detach