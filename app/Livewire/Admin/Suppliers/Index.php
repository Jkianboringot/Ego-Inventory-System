<?php

namespace App\Livewire\Admin\Suppliers;

use App\Models\Supplier;
use Livewire\Component;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class Index extends Component
{
    use WithPagination;
    public string $search = '';

    public $readyToLoad = false;
    protected $paginationTheme = 'bootstrap';

    public function loadData()
    {
        $this->readyToLoad = true;
    }
    
    public function updatingSearch()
    {
        // Reset pagination when search input changes
        $this->resetPage();
    }
    
    function delete($id)
    {
        try {
            $supplier = Supplier::findOrFail(id: $id);
            $supplier->delete();
            $this->dispatch('done', success: "Successfully Deleted.");
        } catch (\Throwable $th) {
            $this->dispatch('done', error: "Something went wrong: " . $th->getMessage());
        }
    }
    
    public function render()
    {
        $search = trim($this->search);
        $suppliers = Supplier::query()
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('suppliers.name', 'like', "%$search%")
                        ->orWhere('suppliers.tax_id', 'like', "%$search%")
                        ->orWhere('suppliers.account_number', 'like', "%$search%");
                });
            })
            ->leftJoin('purchases', 'suppliers.id', '=', 'purchases.supplier_id')
            ->leftJoin('product_purchase', 'purchases.id', '=', 'product_purchase.purchase_id')
            ->select(
                'suppliers.id',
                'suppliers.name',
                'suppliers.address',
                'suppliers.phone_number',
                'suppliers.tax_id',
                'suppliers.account_number',
                'suppliers.created_at',
                'suppliers.updated_at',
                'suppliers.deleted_at'
            )
            ->selectRaw('
                COUNT(DISTINCT CASE WHEN purchases.is_paid = "Paid" AND purchases.deleted_at IS NULL THEN purchases.id END) AS purchase_count,
                COALESCE(SUM(CASE WHEN purchases.is_paid = "Paid" AND product_purchase.deleted_at IS NULL THEN product_purchase.quantity * product_purchase.unit_price END), 0) AS total_amount
            ')
            ->groupBy(
                'suppliers.id',
                'suppliers.name',
                'suppliers.address',
                'suppliers.phone_number',
                'suppliers.tax_id',
                'suppliers.account_number',
                'suppliers.created_at',
                'suppliers.updated_at',
                'suppliers.deleted_at'
            )
            ->orderBy('suppliers.created_at', 'desc')
            ->simplePaginate(10);

        return view('livewire.admin.suppliers.index', [
            'suppliers' => $suppliers
        ]);
    }
}