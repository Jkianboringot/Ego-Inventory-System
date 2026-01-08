<?php

namespace App\Livewire\Admin\AddProducts;

use App\Models\AddProduct;
use App\Traits\AddToList;
use App\Traits\DeleteCartItem;
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
    use DeleteCartItem;
    use UpdatedProductSearch;
    use SelectProduct;
    use LoadedProducts;
    use AddToList;
    protected string $context = 'addproducts';

    public $productSearch;

    public $selectedProductId;

    public $quantity;



    public AddProduct $addProduct;


    public $productList = [];


    function rules()
    {
        return [
            'productList.*.quantity' => 'required|numeric|min:0.01|max:999999', // note: integer → remove decimals
            'quantity' => 'required|numeric|min:0.01|max:999999', // note: integer → remove decimals
            'selectedProductId' => 'required',
            'productList' => 'required',
           
        ];
    }



    function mount()
    {
        $this->addProduct = new AddProduct();
    }

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
            $this->addProduct->status = 'pending';
            $this->addProduct->save();



            foreach ($this->productList as $listItem) {



                $this->addProduct->products()->syncWithoutDetaching([
                    $listItem['product_id'] => [
                        'quantity' => $listItem['quantity'],
                    ],
                ]);

                $this->logs($listItem);
            }

            DB::commit();

            return redirect()->route('admin.add-products.index')
                ->with('success', 'Successfully Created.');
        } catch (\Throwable $th) {
            if (DB::transactionLevel() > 0) {
                DB::rollBack();
            }

            $this->dispatch('done', error: "Something Went Wrong: " . $th->getMessage());
        }
    }

    private function logs(array $listItem)
    {
        \App\Models\ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'stock_added',
            'model' => 'New Arrival',
            'changes' => json_encode([
                'added_quantity' => $listItem['quantity'],
            ]),
            'ip_address' => request()->ip(),
            'user_agent' => request()->header('User-Agent'),
        ]);
    }

    public function render()
    {


        return view(
            'livewire.admin.add-products.create',
            [
                'products' => $this->productSearch(),
                'loadedProducts' => $this->loadedProducts

            ]
        );
    }
}
