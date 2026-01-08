<?php

// ============================================
// Returns Index Component - Minor Fixes
// ============================================

namespace App\Livewire\Admin\Returns;

use App\Models\Pivots\ProductReturn;
use App\Models\ReturnItem;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $readyToLoad = false;
    protected $paginationTheme = 'bootstrap';

    public function loadData()
    {
        $this->readyToLoad = true;
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }
    public function returnToggle($id)
    {


        try {
            $r = ReturnItem::findOrFail($id);

            DB::beginTransaction();
            $r->return_type = true;
            $r->save();
            DB::commit();
            $this->dispatch('done', success: "Successfully Returned.");
        } catch (\Throwable $th) {
            if (DB::transactionLevel() > 0) {
                DB::rollBack();
            }
            $this->dispatch('done', error: "Something went wrong: " . $th->getMessage());
        }
    }
    public function delete($id)
    {
        try {
            $return = ReturnItem::findOrFail($id);
    if (in_array($return->status, ['approved', 'edit_pending'])) {
                  throw new \Exception('Record is Approve already');
            }
            DB::beginTransaction();

            ProductReturn::where('return_item_id', $id)->delete();
            $return->delete();
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

         $returns = ReturnItem::query()
            ->select([
                'return_items.*',   //coalesce just handle the if null logic, if null just be zero else return value
                DB::raw('COALESCE((     
                    SELECT SUM(op.quantity * op.unit_price)
                    FROM product_return op
                    WHERE op.return_item_id = return_items.id
                ), 0) as total_amount')
            ])
            ->when($search, fn($query) =>
                $query->where(function ($sub) use ($search) {
                    $sub->where('return_items.created_at', 'like', "%$search%")
                        ->orWhere('customers.name', 'like', "%$search%");
                })
            )
            ->orderBy('return_items.created_at', 'desc')
            ->simplePaginate(10);


        return view('livewire.admin.returns.index', ['returns' => $returns]);
    }
}
