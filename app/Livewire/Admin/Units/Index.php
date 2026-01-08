<?php

namespace App\Livewire\Admin\Units;

use App\Models\Unit;
use App\Traits\LoadData;
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
    public function updatingSearch()
    {
        // Reset pagination when search input changes
        $this->resetPage();
    }
  function delete($id)
{
    try {
        $unit = Unit::findOrFail($id);

        if ($unit->products()->exists()) {
                throw new \Exception("Cannot delete: unit has products");
            }

        $unit->delete();

        $this->dispatch('done', success: "Successfully Deleted.");
    } catch (\Throwable $th) {
        $this->dispatch('done', error: "Something went wrong: " . $th->getMessage());
    }
}



    public function render()
    {


        $search = trim($this->search);

        $units = Unit::when(
            $search,
            fn($query) =>
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%");
            })
        )
            ->withCount('products')
            ->orderBy('created_at', 'desc')
            ->simplePaginate(10);

        return view('livewire.admin.units.index', [
            'units' => $units
        ]);
    }
}
