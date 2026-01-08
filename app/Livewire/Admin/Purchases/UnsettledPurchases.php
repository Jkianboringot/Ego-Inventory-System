<?php

namespace App\Livewire\Admin\Purchases;

use App\Models\ActivityLog;
use App\Models\Pivots\ProductPurchase;
use App\Models\Purchase;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class UnsettledPurchases extends Component
{
    use WithPagination;

    public $search = '';
    public $readyToLoad = false;
    protected $paginationTheme = 'bootstrap';

    public function loadData()
    {
        $this->readyToLoad = true;
    }

    public function isPaid($id)
    {
        try {
            DB::transaction(function () use ($id) {
                $purchase = Purchase::findOrFail($id);

                if (in_array($purchase->is_paid, ['Paid', 'Partially Paid'])) {
                    throw new \Exception("This purchase is already marked as paid or partial.");
                }

                $purchase->update([
                    'is_paid' => 'Paid',
                    'date_settled' => now()
                    
                ]);
                 ActivityLog::create([
                    'user_id'   => auth()->id(),
                    'action'    => 'Change Status To Paid',
                    'model'     => 'Purchase',
                    'model_id'  => $purchase->id,
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->header('User-Agent'),
                ]);
            });

            $this->dispatch('done', success: "Purchase Paid");
        } catch (\Throwable $th) {
            $this->dispatch('done', error: "Something went wrong: " . $th->getMessage());
        }
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
            ->where('purchases.is_paid', '!=', 'Paid')
            ->orderBy('purchases.created_at', 'desc')
            ->simplePaginate(10);

        return view('livewire.admin.purchases.index', [
            'purchases' => $purchases
        ]);
    }
}