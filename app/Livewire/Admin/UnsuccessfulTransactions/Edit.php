<?php

namespace App\Livewire\Admin\UnsuccessfulTransactions;

use App\Models\EditApproval;
use App\Models\Product;
use App\Models\UnsuccessfulTransaction;
use App\Models\Supplier;
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

class Edit extends Component
{
    use ProductSearch;
    use WithCancel;
    use UpdatedProductSearch;

    use DeleteCartItem;
    use SelectProduct;


    public $productSearch;
    protected string $context = 'unsuccessfull';


    public $selectedProductId;

    public $quantity;


    public UnsuccessfulTransaction $unsuccessfulTransaction;
    public $productList = [];


    function rules()
    {
        return [
            
            'productList.*.quantity' => 'required|numeric|min:0.01|max:999999', // note: numeric → remove decimals

            'quantity' => 'required|numeric|min:0.01|max:999999.99',
            'selectedProductId' => 'required',
            'productList' => 'required'


        ];
    }



    function mount($id)
    {
        $this->unsuccessfulTransaction = UnsuccessfulTransaction::findOrFail($id);
        //got rid of eager loading with unit
        $this->productList = $this->unsuccessfulTransaction->products->map(fn($product) => [
            'product_id' => $product->id,
            'quantity' => $product->pivot->quantity,
        ])->toArray();
    }



    function selectProduct($id)
    {
        $this->selectedProductId = $id;
        $this->productSearch = Product::find($id)->name;
    }
    function addToList()
    {
        $this->validateOnly('selectedProductId');
        $this->validateOnly('quantity');
        try {
            // if (empty($this->unsuccessfulTransaction->unsuccessful_transactions_date)) {
            //     $this->unsuccessfulTransaction->unsuccessful_transactions_date = now()->toDateString();
            // }           // Manual checks to replace validate()


            foreach ($this->productList as $key => $item) {
                if ($item['product_id'] == $this->selectedProductId) {
                    $this->productList[$key]['quantity'] += $this->quantity;
                    $this->reset(['selectedProductId', 'productSearch', 'quantity']);
                    return;
                }
            }

            // Add new item to list
            $this->productList[] = [
                'product_id' => $this->selectedProductId,
                'quantity' => $this->quantity,
            ];

            // Clean up input fields
            $this->reset(['selectedProductId', 'productSearch', 'quantity']);
        } catch (\Throwable $th) {
            $this->dispatch('done', error: "Something went wrong: " . $th->getMessage());
        }
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


            $changes = [
                'From' => 'Transactions',

                'edited_date' => $this->unsuccessfulTransaction->created_at,
                'products' => $this->productList,
            ];
            $this->unsuccessfulTransaction->status = 'edit_pending';

            EditApproval::create([
                'user_id'       => auth()->id(),
                'editable_id'   => $this->unsuccessfulTransaction->id,
                'editable_type' => UnsuccessfulTransaction::class,
                'changes'       => $changes,
                'status'        => 'edit_pending',
            ]);

            $this->unsuccessfulTransaction->update(); //this was where and working ['status' => 'pending'] just got rid of it sep 26 2am

            $this->unsuccessfulTransaction->products()->detach();

            foreach ($this->productList as $item) {
                $this->unsuccessfulTransaction->products()->attach($item['product_id'], [
                    'quantity'   => $item['quantity'],
                ]);
            }

            DB::commit();

            return to_route('admin.unsuccessful-transactions.index')
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
            'livewire.admin.unsuccessful-transactions.edit',
            [
                'products' => $this->ProductSearch() ?? []
            ]

        );
    }
}

// if something brike look at make unsuccessfulTransaction funstion i change it from atteach to detach