<?php

namespace App\Livewire\Admin\Defected;

use App\Models\Defected;
use App\Models\Pivots\DefectedToList;
use App\Traits\LoadData;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

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

    function delete($id)
    {
        try {



            DB::transaction(function () use ($id) {
                $defect = Defected::findOrFail($id);
                DefectedToList::where('defected_id', $id)->delete();
                $defect->delete();
            });



            $this->dispatch('done', success: "Successfully Deleted.");
        } catch (\Throwable $th) {
            //throw $th;
            $this->dispatch('done', error: "Something went wrong: " . $th->getMessage());
        }
    }


    public function render()
    {
        $search = trim($this->search);

        $defects = Defected::query()
            ->select('defecteds.*')
            ->with([
                'products:id,name,barcode'

            ])
            ->withSum('products as total_quantity', 'defecteds_to_list.quantity')
            ->when(
                $search,
                fn($query) =>
                $query->where(function ($sub) use ($search) {
                    $sub->where('defecteds.created_at', 'like', "%$search%");
                })
            )
            ->orderBy('defecteds.created_at', 'desc')
            ->simplePaginate(10);

        return view('livewire.admin.defected.index', [
            'defects' => $defects,
        ]);
    }
}
