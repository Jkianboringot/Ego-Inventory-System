<?php

namespace App\Livewire\Admin\AddProducts;

use App\Models\AddProduct;
use App\Models\Pivots\AddProductToList;
use App\Models\Product;
use App\Traits\LoadData;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class Index extends Component
{
    use WithPagination;
    use LoadData;
    protected $paginationTheme = 'bootstrap';

    public string $search = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }


    public function delete($id)
    {
        try {
            $addproduct = AddProduct::findOrFail($id);
            if (in_array($addproduct->status, ['approved', 'edit_pending'])) {
                  throw new \Exception('Record is Approve already');
            }

            DB::beginTransaction();

            AddProductToList::where('add_product_id', $id)->delete();
            $addproduct->delete();
            DB::commit();


            $this->dispatch('done', success: "Successfully Deleted.");
        } catch (\Throwable $th) {
            if (DB::transactionLevel() > 0) {
                DB::rollBack();
            }
            $this->dispatch('done', error: "Something went wrong: " . $th->getMessage());
        }
    }


    public function render()
    {
        $search = trim($this->search);

        $addProducts = AddProduct::query()
            ->select('add_products.*')
            ->with([
                'products:id,name,barcode'

            ])
            ->withSum('products as total_quantity', 'add_products_to_list.quantity')
            ->when(
                $search,
                fn($query) =>
                $query->where(function ($sub) use ($search) {
                    $sub->where('add_products.created_at', 'like', "%$search%")
                        ->orWhere('add_products.status', 'like', "%$search%");
                })
            )
            ->orderBy('add_products.created_at', 'desc')
            ->simplePaginate(10);

        return view('livewire.admin.add-products.index', [
            'addProducts' => $addProducts,
        ]);
    }
    //         public function render()
    // {
    //     $search = trim($this->search);

    //     $addProducts = AddProduct::select('add_products.*')
    //         ->when($search, fn($query) =>
    //             $query->where(function($sub) use ($search) {
    //                 $sub->where('add_products.created_at', 'like', "%$search%")
    //                 ->orWhere('add_products.status','like',"%$search%");
    //             })
    //         )
    //         ->with(['products'])
    //         ->orderBy('add_products.created_at', 'desc')
    //         ->simplePaginate(10);

    //     return view('livewire.admin.add-products.index', [
    //         'addProducts' => $addProducts,
    //     ]);
    // }


}
