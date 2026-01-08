<?php

namespace App\Livewire\Admin\Sales;

use App\Models\ActivityLog;
use App\Models\Pivots\ProductSale;
use App\Models\Sale;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class Index extends Component
{
    use WithPagination;

    public string $search = '';
    protected $paginationTheme = 'bootstrap';
    public $op = false;
    public $readyToLoad = false;

    public function loadData()
    {
        $this->readyToLoad = true;
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    // public function statusToggleReturn($id)
    // {
    //     //this is just for storing an activity log and updating a column
    //     try {
    //         $sale = Sale::findOrFail($id);

    //         DB::beginTransaction();


    //         if (!$sale->return_status) {
    //             $sale->return_status = true;
    //             $sale->save(); // Use save() instead of update()
    //             ActivityLog::create([
    //                 'user_id'   => auth()->id(),
    //                 'action'    => 'Returned',
    //                 'model'     => 'Sales',
    //                 'ip_address' => request()->ip(),
    //                 'user_agent' => request()->header('User-Agent'),
    //             ]);
    //         }
    //         DB::commit();


    //         $this->dispatch('done', success: "Successfully changed this sale status");
    //     } catch (\Throwable $th) {
    //         if (DB::transactionLevel() > 0) {
    //             DB::rollBack();
    //         }
    //         $this->dispatch('done', error: "Something went wrong: " . $th->getMessage());
    //     }
    // }


    public function delete($id)
    {
        try {
            DB::transaction(function () use ($id) {
                $sale = Sale::findOrFail($id);
                ProductSale::where('sale_id', $id)->delete();
                $sale->delete();
            });

            $this->dispatch('done', success: "Successfully Deleted.");
        } catch (\Throwable $th) {
            $this->dispatch('done', error: "Something went wrong: " . $th->getMessage());
        }
    }

    public function render()
    {
        $search = trim($this->search);
        $user = auth()->user();
        if ($user->hasPermission('Admin') || $user->hasPermission("Supervisor")) {
            $this->op = true;
        }

        $sales = Sale::query()
            ->select([
                'sales.*',
                DB::raw('COALESCE((
                    SELECT SUM(ps.quantity * ps.unit_price)
                    FROM product_sale ps
                    WHERE ps.sale_id = sales.id
                ), 0) as total_amount'),
                DB::raw('COALESCE((
                    SELECT SUM(ps.quantity)
                    FROM product_sale ps
                    WHERE ps.sale_id = sales.id
                ), 0) as total_quantity')
            ])
            ->leftJoin('customers', 'sales.customer_id', '=', 'customers.id')
            ->with([
                'customer:id,name',
                'products:id,name,barcode' // Still needed for display
            ])
            ->when(
                $search,
                fn($query) =>
                $query->where(function ($sub) use ($search) {
                    $sub->where('sales.created_at', 'like', "%$search%")
                        ->orWhere('customers.name', 'like', "%$search%")
                        ->orWhere('sales.sales_ref', 'like', "%$search%");
                })
            )
            ->orderBy('sales.id', 'desc')
            ->simplePaginate(10);

        $pageTotals = [
            'quantity' => $sales->sum('total_quantity'),
            'amount' => $sales->sum('total_amount')
        ];

        return view('livewire.admin.sales.index', [
            'sales' => $sales,
            'pageTotals' => $pageTotals, // Use in totals row
        ]);
    }
}
