<?php

namespace App\Livewire\Admin\UnsuccessfulTransactions;

use App\Models\Product;
use App\Models\UnsuccessfulTransaction;
use App\Models\Supplier;
use App\Traits\AddToList;
use App\Traits\AddToQuantity;
use App\Traits\DeleteCartItem;
use App\Traits\ProductSearch;
use App\Traits\SelectProduct;
use App\Traits\SubToQuantity;
use App\Traits\UpdatedProductSearch;
use App\Traits\WithCancel;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Livewire\Component;

class Create extends Component
{
    use ProductSearch;
    use WithCancel;
    use DeleteCartItem;
    use SelectProduct;
    use UpdatedProductSearch;
    use AddToList;

    public $productSearch;

    public $selectedProductId;

    public $quantity;


    protected string $context = 'unsuccessfull';

    public UnsuccessfulTransaction $unsuccessfulTransaction;


    public $productList = [];


    public $loadedProducts = [];

    function rules()
    {
        return [
             
            'productList.*.quantity' => 'required|numeric|min:0.01|max:999999', // note: numeric → remove decimals

            'quantity' => 'required|numeric|min:0.01|max:999999',
            'selectedProductId' => 'required',
            'productList' => 'required',
        ];
    }



    function mount()
    {
        $this->unsuccessfulTransaction = new UnsuccessfulTransaction();
    }



    public function updatedProductList()
    {
        $ids = collect($this->productList)->pluck('product_id')->unique();
        $this->loadedProducts = Product::whereIn('id', $ids)->get();
        //got rid of eager loaded with unit
    }


    //    public function updated($propertyName)
    // {    try using this in more thatn the jsut validate this is better becuae its more focus and dont rerun all rules like validate with everykey stroke 
    //     $this->validateOnly($propertyName);
    // }




    function save()
    {
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

            $this->unsuccessfulTransaction->status = 'pending';

            $this->unsuccessfulTransaction->save();


            foreach ($this->productList as $listItem) {
                $this->unsuccessfulTransaction->products()->syncWithoutDetaching([
                    $listItem['product_id'] => [
                        'quantity' => $listItem['quantity'],
                    ],
                ]);



                //make this a function
                \App\Models\ActivityLog::create([
                    'user_id' => auth()->id(),
                    'action' => 'stock_added',
                    'model' => 'Unsuccessful Transaction',
                    'changes' => json_encode([
                        'added_quantity' => $listItem['quantity'],
                    ]),
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->header('User-Agent'),
                ]);
            }
            DB::commit();

            return to_route('admin.unsuccessful-transactions.index')
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

        return view(
            'livewire.admin.unsuccessful-transactions.create',
            [
                'products' => $this->ProductSearch(),
                'loadedProducts' => $this->loadedProducts

            ]
        );
    }
}
