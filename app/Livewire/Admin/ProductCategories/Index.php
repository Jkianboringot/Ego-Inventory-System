<?php

namespace App\Livewire\Admin\ProductCategories;

use App\Models\ProductCategory;
use App\Traits\LoadData;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class Index extends Component
{

    use WithPagination;
    public string $search = '';


    use LoadData;
    protected $paginationTheme = 'bootstrap';


    function delete($id)
    {
        try {
            $category = ProductCategory::findOrFail($id);
            if ($category->products()->exists()) {
                throw new \Exception("Cannot delete: category has products");
            }



            $category->delete();

            $this->dispatch('done', success: "Successfully Deleted.");
        } catch (\Throwable $th) {
            //throw $th;
            $this->dispatch('done', error: "Something went wrong: " . $th->getMessage());
        }
    }
    public function render()
    {

        $search = trim($this->search);

        $productCategories = ProductCategory::when(
            $search,
            fn($query) =>
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%");
            })
        )
            ->withCount('products')
            ->orderBy('created_at', 'desc')
            ->simplePaginate(10);


        return view(
            'livewire.admin.product-categories.index',
            [
                'productCategories' => $productCategories
            ]
        );
    }
}
// 'productCategories' refers to the foreach in index.blade it prettry much the variable for evrything in prodoctCategory
// or variable for te frontend