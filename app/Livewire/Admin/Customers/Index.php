<?php

namespace App\Livewire\Admin\Customers;

use App\Models\Customer;
use App\Traits\LoadData;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class Index extends Component
{
    use WithPagination;
    use LoadData;

    public $op = false;
    public string $search = '';

    protected $paginationTheme = 'bootstrap';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    function delete($id)
    {
        try {
            $customer = Customer::findOrFail($id);
            $customer->delete();
            $this->dispatch('done', success: "Successfully Deleted.");
        } catch (\Throwable $th) {
            $this->dispatch('done', error: "Something went wrong: " . $th->getMessage());
        }
    }

    public function render()
    {
        $search = trim($this->search);

        $currentUser = auth()->user();
        $this->op = $currentUser->hasPermission('Admin') || $currentUser->hasPermission("Supervisor");

        $customers = Customer::query()
            ->when($search, function ($query) use ($search) {
                $query->where('customers.name', 'like', "%$search%")
                      ->orWhere('customers.organization_type', 'like', "%$search%")
                      ->orWhere('customers.tax_id', 'like', "%$search%")
                      ->orWhere('customers.phone_number', 'like', "%$search%");
            })
            ->orderBy('customers.created_at', 'desc')
            ->simplePaginate(10);

        $customerIds = $customers->pluck('id')->toArray();

        $counts = Customer::query()
            ->whereIn('customers.id', $customerIds)
            ->leftJoin('sales', 'customers.id', '=', 'sales.customer_id')
            ->leftJoin('product_sale', 'sales.id', '=', 'product_sale.sale_id')
            ->leftJoin('orders', 'customers.id', '=', 'orders.customer_id')
            ->leftJoin('order_product', 'orders.id', '=', 'order_product.order_id')
            ->select('customers.id')
            ->selectRaw('
                COUNT(DISTINCT CASE WHEN sales.deleted_at IS NULL THEN sales.id END) AS sales_count,
                COALESCE(SUM(CASE WHEN product_sale.deleted_at IS NULL THEN product_sale.quantity * product_sale.unit_price END), 0) AS total_sale_amount,
                COUNT(DISTINCT CASE WHEN orders.deleted_at IS NULL AND orders.order_status = 0 THEN orders.id END) AS orders_count,
                COALESCE(SUM(CASE WHEN order_product.deleted_at IS NULL AND orders.order_status = 0 THEN order_product.quantity * order_product.unit_price END), 0) AS total_order_amount
            ')
            ->groupBy('customers.id')
            ->get()
            ->keyBy('id'); // Key by customer id for easy access

        return view('livewire.admin.customers.index', [
            'customers' => $customers,
            'counts' => $counts,
        ]);
    }
}
