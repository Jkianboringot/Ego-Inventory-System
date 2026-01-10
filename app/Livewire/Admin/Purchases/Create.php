<?php

namespace App\Livewire\Admin\Purchases;

use App\Models\ActivityLog;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Supplier;
use App\Traits\AddToList;
use App\Traits\DeleteCartItem;
use App\Traits\LoadedProducts;
use App\Traits\ProductSearch;
use App\Traits\SelectSupplier;
use App\Traits\UpdatedProductSearch;
use App\Traits\WithCancel;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class Create extends Component
{
    use UpdatedProductSearch;

    use ProductSearch;
    use WithCancel;
    use DeleteCartItem;
    use SelectSupplier;
    use LoadedProducts;
    // use AddToList;

    public $supplierSearch;
    protected string $context = 'purchases';
    public $productSearch;

    public $selectedProductId;

    public $quantity;
    public $price;


    public Purchase $purchase;
    public $productList = [];

    function rules()
    {
        $paid = in_array($this->purchase->is_paid, ['Paid', "Partially_Paid"])
            ? ['purchase.date_settled' => 'required']
            : ['purchase.date_settled' => 'nullable'];

        return array_merge([
            'purchase.is_paid'       => 'required',
            'quantity' => 'required|min:0.01|max:999999.99',
            'selectedProductId' => 'required',
            'price' => 'required|min:0.01|max:999999.99',
            'productList' => 'required',
            'productList.*.quantity' => 'required|numeric|min:0.01|max:999999', // note: numeric → remove decimals
        ], $paid);
    }




    function mount()
    {
        $this->purchase = new Purchase();
        $this->purchase->is_paid = 'Unpaid';
    }

    function addToList()
    {
        $this->validateOnly('price');

        $this->validateOnly('quantity');
        $this->validateOnly('selectedProductId');


        try {



            foreach ($this->productList as $key => $listItem) {
                if ($listItem['product_id'] == $this->selectedProductId && $listItem['price'] == $this->price) {
                    $this->productList[$key]['quantity'] += $this->quantity;
                    $this->productList[$key]['price'] += $this->price;
                    return;
                    # code...gs

                }
            }


            array_push($this->productList, [
                'product_id' => $this->selectedProductId,
                'quantity' => $this->quantity,
                'price' => $this->price,



            ]);


            $this->updatedProductList();


            $this->reset([
                'selectedProductId',
                'productSearch',
                'quantity',
                'price',
            ]);
        } catch (\Throwable $th) {
            $this->dispatch('done', error: "Something went wrong: " . $th->getMessage());
        }
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


    //    public function updated($propertyName)
    // {    try using this in more thatn the jsut validate this is better becuae its more focus and dont rerun all rules like validate with everykey stroke 
    //     $this->validateOnly($propertyName);
    // }
    // function updated()
    // { //just temporary
    //     $this->validate();
    // }
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

            $this->purchase->save();

            foreach ($this->productList as $key => $listItem) {
                $this->purchase->products()->attach($listItem['product_id'], [
                    'quantity'   => $listItem['quantity'],
                    'unit_price' => $listItem['price'],
                ]);

                // ✅ Log activity
                ActivityLog::create([
                    'user_id'    => auth()->id(),
                    'action'     => 'purchase_product_added',
                    'model'      => 'Purchase',
                    'changes'    => json_encode([
                        'product_id' => $listItem['product_id'],
                        'quantity'   => $listItem['quantity'],
                        'unit_price' => $listItem['price'],
                    ]),
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->header('User-Agent'),
                ]);
            }
            DB::commit();

            return redirect()->route('admin.purchases.index')
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
            'livewire.admin.purchases.create',
            [
                'suppliers' => $suppliers,
                'products' => $this->productSearch(),
                'loadedProducts' => $this->loadedProducts,

                'paidOptions' => $paidOptions,

            ]
        );
    }
}
