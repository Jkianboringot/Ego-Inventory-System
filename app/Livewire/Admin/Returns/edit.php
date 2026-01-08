<?php

namespace App\Livewire\Admin\Returns;

use App\Models\EditApproval;
use App\Models\Product;
use App\Models\ReturnItem;
use App\Traits\AddToList;
use App\Traits\AddToQuantity;
use App\Traits\DeleteCartItem;
use App\Traits\GetProductWithInventory;
use App\Traits\ProductSearch;
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
    use WithCancel;
    use SelectProduct;
    use UpdatedProductSearch;
    use AddToList;
    use GetProductWithInventory;
    use DeleteCartItem;

    public $productSearch;
    public $selectedProductId;

    public $quantity;
    public $adds_on;
    public $price;
    public ReturnItem $return;
    protected string $context = 'returns';

    public $productList = [];

    protected function rules(): array
    {
        return [
            'return.return_type' => [
                'required',
                Rule::in(['refunded', 'exchanged']),
            ],
            'price' => 'required|min:0.01|max:999999.99',
            'quantity' => 'required|numeric|min:0.01|max:999999.99',
            'adds_on' => 'nullable|max:999999.99',

            'selectedProductId' => 'required',
            'return.reason' => 'required|max:255|min:0.015',
            'productList' => 'required',
            'productList.*.quantity' => 'required|numeric|min:0.01|max:999999', // note: numeric → remove decimals
        ];
    }



    function mount($id)
    {
        $this->return = ReturnItem::with('products')->findOrFail($id);

        $this->productList = $this->return->products->map(fn($p) => [
            'product_id' => $p->id,
            'quantity'   => $p->pivot->quantity,
            'price'   => $p->pivot->unit_price,
            'adds_on'   => $p->pivot->adds_on,

        ])->toArray();
    }



    // function addToList()
    // {
    //     $this->validateOnly('selectedProductId');
    //     $this->validateOnly('quantity');
    //     $this->validateOnly('price');
    //     $this->validateOnly('adds_on');
    //     try {




    //         foreach ($this->productList as $key => $listItem) {
    //             if ($listItem['product_id'] == $this->selectedProductId && $listItem['price'] == $this->price) {
    //                 $this->productList[$key]['quantity'] += $this->quantity;
    //                 $this->productList[$key]['price'] += $this->price;
    //                 $this->productList[$key]['adds_on'] += $this->adds_on;
    //                 return;
    //                 # code...gs

    //             }
    //         }


    //         array_push($this->productList, [
    //             'product_id' => $this->selectedProductId,
    //             'quantity' => $this->quantity,
    //             'price' => $this->price,
    //             'adds_on' => $this->adds_on,



    //         ]);




    //         $this->reset([
    //             'selectedProductId',
    //             'productSearch',
    //             'quantity',
    //             'price',
    //             'adds_on',
    //         ]);
    //     } catch (\Throwable $th) {
    //         $this->dispatch('done', error: "Something went wrong: " . $th->getMessage());
    //     }
    // }


    // function deleteCartItem($key)
    // {
    //     array_splice($this->productList, $key, 1);
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


            $changes = [
                'From' => 'Returns',
                'edited_date' => $this->return->created_at,
                'products' => $this->productList,
            ];


            $this->return->status = 'edit_pending';

            EditApproval::create([
                'user_id'       => auth()->id(),
                'editable_id'   => $this->return->id,
                'editable_type' => ReturnItem::class,
                'changes'       => $changes,
                'status'        => 'edit_pending',
            ]);


            $this->return->update();

            // Refresh the product associations
            $this->return->products()->detach();

            foreach ($this->productList as $item) {
                $this->return->products()->attach($item['product_id'], [
                    'quantity'   => $item['quantity'],
                    'unit_price' => $item['price'],
                    'adds_on' => $item['adds_on'],
                ]);
            }


            DB::commit();

            return redirect()->route('admin.returns.index')
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
        $loadedProducts = Product::whereIn('id', collect($this->productList)->pluck('product_id'))->get();

        return view('livewire.admin.returns.edit', [
            'products' => $this->productSearch(),
            'loadedProducts' => $loadedProducts,
        ]);
    }
}
