<?php

namespace App\Livewire\Admin\AddProducts;

use App\Models\ActivityLog;
use App\Models\EditApproval;
use App\Models\AddProduct;
use App\Traits\DeleteCartItem;
use App\Traits\ProductSearch;
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
    use WithCancel;
    use DeleteCartItem;
    use UpdatedProductSearch;

    use SelectProduct;
    public $productSearch;

    public $selectedProductId;
    protected string $context = 'addproducts';

    public $quantity;


    public AddProduct $addProduct;
    public $productList = [];


    function rules()
    {
        return [
            'productList.*.quantity' => 'required|numeric|min:0.01|max:999999', // note: integer → remove decimals

            'quantity' => 'required|numeric|min:0.01|max:999999.99',
            'selectedProductId' => 'required',
            'productList' => 'required',
            
        ];
    }



    function mount($id)
    {


        $this->addProduct = AddProduct::findOrFail($id);

        $this->productList = $this->addProduct->products->map(fn($product) => [
            'product_id' => $product->id,
            'quantity' => $product->pivot->quantity,
        ])->toArray();
    }





    function addToList()
    {
        $this->validateOnly('selectedProductId');
        $this->validateOnly('quantity');
        try {

            foreach ($this->productList as $key => $item) {
                if ($item['product_id'] == $this->selectedProductId) {
                    $this->productList[$key]['quantity'] += $this->quantity;
                    $this->reset(['selectedProductId', 'productSearch', 'quantity']);
                    return;
                }
            }

            $this->productList[] = [
                'product_id' => $this->selectedProductId,
                'quantity' => $this->quantity,
            ];

            $this->reset(['selectedProductId', 'productSearch', 'quantity']);
        } catch (\Throwable $th) {
            $this->dispatch('done', error: "Something went wrong: " . $th->getMessage());
        }
    }


    protected function activityLog()
    {

        $changes = [
            'From' => 'New Arrivals',

            'edited_date' => $this->addProduct->created_at,
            'products' => $this->productList,
        ];


        EditApproval::create([
            'user_id'       => auth()->id(),
            'editable_id'   => $this->addProduct->id,
            'editable_type' => AddProduct::class,
            'changes'       => $changes,
            'status'        => 'edit_pending',
        ]);

        ActivityLog::create([
            'user_id'   => auth()->id(),
            'action'    => 'Updated',
            'model'     => 'AddProduct',
            'changes'   => json_encode($changes),
            'ip_address' => request()->ip(),
            'user_agent' => request()->header('User-Agent'),
        ]);
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

            $this->activityLog();
            $this->addProduct->status = 'edit_pending';

            $this->addProduct->update();

            $this->addProduct->products()->detach();

            foreach ($this->productList as $item) {

                $this->addProduct->products()->attach($item['product_id'], [
                    'quantity'   => $item['quantity'],
                ]);
            }



            DB::commit();

            return redirect()->route('admin.add-products.index')
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


        return view(
            'livewire.admin.add-products.edit',
            [
                'products' => $this->ProductSearch()
            ]

        );
    }
}

// if something brike look at make addProduct funstion i change it from atteach to detach