<?php

namespace App\Livewire\Admin\Purchases;

use App\Models\ActivityLog;
use App\Models\Pivots\ProductPurchase;
use App\Models\Purchase;
use App\Traits\LoadData;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class Index extends Component
{
    use WithPagination, LoadData;

    public string $search = '';
    protected $paginationTheme = 'bootstrap';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function delete($id)
    {
        try {
            DB::transaction(function () use ($id) {
                $purchase = Purchase::findOrFail($id);
                ProductPurchase::where('purchase_id', $id)->delete();
                $purchase->delete();
            });

            
            $this->dispatch('done', success: "Successfully Deleted.");
        } catch (\Throwable $th) {
            $this->dispatch('done', error: "Something went wrong: " . $th->getMessage());
        }
    }

    public function render()
    {
        $search = trim($this->search);

        // ✅ Calculate total_amount in database
        $purchases = Purchase::query()
            ->select([
                'purchases.*',
                DB::raw('COALESCE((
                    SELECT SUM(pp.quantity * pp.unit_price)
                    FROM product_purchase pp
                    WHERE pp.purchase_id = purchases.id
                ), 0) as total_amount')
            ])
            ->leftJoin('suppliers', 'purchases.supplier_id', '=', 'suppliers.id')
            ->with(['supplier:id,name,address,tax_id'])
            ->when($search, fn($query) =>
                $query->where(function ($sub) use ($search) {
                    $sub->where('purchases.created_at', 'like', "%$search%")
                        ->orWhere('suppliers.name', 'like', "%$search%")
                        ->orWhere('purchases.is_paid', 'like', "%$search%");
                })
            )
            ->where('purchases.is_paid', 'Paid')
            ->orderBy('purchases.created_at', 'desc')
            ->simplePaginate(10);

        return view('livewire.admin.purchases.index', [
            'purchases' => $purchases
        ]);
    }
}