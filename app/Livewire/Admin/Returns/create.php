<?php

namespace App\Livewire\Admin\Returns;

use App\Models\ReturnItem;
use App\Traits\AddToList;
use App\Traits\DeleteCartItem;
use App\Traits\GetProductWithInventory;
use App\Traits\LoadedProducts;
use App\Traits\ProductSearch;
use App\Traits\SelectProduct;
use App\Traits\UpdatedProductSearch;
use App\Traits\WithCancel;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class Create extends Component
{
    use ProductSearch;
    use WithCancel;
    use SelectProduct;
    use LoadedProducts;
    use UpdatedProductSearch;
    use AddToList;
    use GetProductWithInventory;
    use DeleteCartItem;

    public $productSearch;
    protected string $context = 'returns';

    public $selectedProductId;

    public $quantity;
    public $price;
    public $adds_on;

    public ReturnItem $return;
    public $productList = [];

    function rules()
    {
        return [
           
            'return.return_type' => [
                'required',
                Rule::in(['refunded', 'exchanged']),
            ],
            'quantity' => 'required|numeric|min:0.01|max:999999',
            'selectedProductId' => 'required',
            'price' => 'required|min:0.01|max:999999.99',
            'adds_on' => 'nullable|min:0.01|max:999999.99',

            'return.reason' => 'required|max:255|min:3',
            'return.sale_invoice' => 'nullable|max:50|min:1',
            'productList' => 'required',
            'productList.*.quantity' => 'required|numeric|min:0.01|max:999999', // note: numeric → remove decimals
        ];
    }
    function mount()
    {
        $this->return = new ReturnItem();
    }





    function save()
    {

        $this->validateOnly('productList');
        $this->validateOnly('productList.*.quantity');
        $this->validateOnly('return.status');

        try {
            foreach ($this->productList as $listItem) {

                if ($listItem['quantity'] <= 0) {
                    $this->dispatch('done', error: "Quantity cannot be zero");
                    return;
                }
            }
            DB::beginTransaction();


            $this->return->status = 'pending';

            $this->return->save();

            foreach ($this->productList as $item) {
                $this->return->products()->syncWithoutDetaching([
                    $item['product_id'] => ['quantity' => $item['quantity'],
                    'unit_price' => $item['price'],
                    'adds_on' => $item['adds_on'],
                    ]
                    
                ]); 
            }


            DB::commit();


            return redirect()->route('admin.returns.index')
                ->with('success', 'Successfully Created.');
        } catch (\Throwable $th) {
            if (DB::transactionLevel() > 0) {
                DB::rollBack();
            }
            $this->dispatch('done', error: "Error: " . $th->getMessage());
        }
    }
    //    public function updated($propertyName)
    // {    try using this in more thatn the jsut validate this is better becuae its more focus and dont rerun all rules like validate with everykey stroke 
    //     $this->validateOnly($propertyName);
    // }

    public function render()
    {
        
        return view('livewire.admin.returns.create', [
            'products' => $this->productSearch(),
            'loadedProducts' => $this->loadedProducts

        ]);
    }
}
