<?php

namespace App\Livewire\Admin\Brands;

use App\Models\Brand;
use App\Traits\LoadData;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class Index extends Component
{
    use WithPagination;
    use LoadData;
    public string $search = '';
    protected $paginationTheme = 'bootstrap';

    public function updatingSearch()
    {
        // Reset pagination when search input changes
        $this->resetPage();
    }
    function delete($id)
    {
        try {
            $brand = Brand::findOrFail($id);
          if ($brand->products()->exists()) {
                throw new \Exception("Cannot delete: brand has products");
            }


            // if ($brand->logo_path && Storage::disk('public')->exists($brand->logo_path)) {
            //     Storage::disk('public')->delete($brand->logo_path);
            // }
            $brand->delete();

            $this->dispatch('done', success: "Successfully Deleted.");
        } catch (\Throwable $th) {
            //throw $th;
            $this->dispatch('done', error: "Something went wrong: " . $th->getMessage());
        }
    }
    public function render()
    {
        $search = trim($this->search);

        $brand = Brand::when(
            $search,
            fn($query) =>
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%");
            })
        )
            ->withCount('products')

            ->orderBy('created_at', 'desc')
            ->simplePaginate(10);

        return view('livewire.admin.brands.index', [
            'brands' => $brand
        ]);
    }
}
