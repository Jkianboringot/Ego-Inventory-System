<?php

namespace App\Livewire\Admin\UnsuccessfulTransactions;

use App\Models\Pivots\UnsuccessfulTransactionToList;
use App\Models\UnsuccessfulTransaction;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class Index extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    
    public string $search = '';

    public $readyToLoad = false;
    public int $totalQuantity = 0;

    public function loadData()
    {
        $this->readyToLoad = true;
    }
    public function updatingSearch(): void
    {
        $this->resetPage();
    }



    public function delete($id)
    {
        try {
            $u = UnsuccessfulTransaction::findOrFail($id);
            if (in_array($u->status, ['approved', 'edit_pending'])) {
                throw new \Exception('Record is Approve already');
            }
            DB::beginTransaction();

            UnsuccessfulTransactionToList::where('unsuccessful_transaction_id', $id)->delete();
            $u->delete();
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

        $unsuccessfulTransactions = UnsuccessfulTransaction::query()
            ->select('unsuccessful_transactions.*')
            ->with([
                'products:id,name,barcode'

            ])
            ->withSum('products as total_quantity', 'unsuccessful_transactions_to_list.quantity')
            ->when(
                $search,
                fn($query) =>
                $query->where(function ($sub) use ($search) {
                    $sub->where('unsuccessful_transactions.created_at', 'like', "%$search%")
                        ->orWhere('unsuccessful_transactions.status', 'like', "%$search%");
                })
            )
            ->orderBy('unsuccessful_transactions.created_at', 'desc')
            ->simplePaginate(10);
        return view('livewire.admin.unsuccessful-transactions.index', [
            'unsuccessfulTransactions' => $unsuccessfulTransactions,
        ]);
    }
}
