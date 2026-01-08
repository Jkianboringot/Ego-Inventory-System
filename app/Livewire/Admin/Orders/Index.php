<?php

namespace App\Livewire\Admin\Orders;

use App\Models\ActivityLog;
use App\Models\Order;
use App\Models\Pivots\OrderProduct;
use App\Models\Pivots\UnsuccessfulTransactionToList;
use App\Models\UnsuccessfulTransaction;
use App\Traits\LoadData;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class Index extends Component
{
    use WithPagination, LoadData;

    protected $paginationTheme = 'bootstrap';
    public string $search = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    // public function statusToggle($id)
    // { this is for making an order unsuccessffull
    //     try {
    //         DB::transaction(function () use ($id) {
    //             $order = Order::findOrFail($id);
                
    //             if (!$order->order_status) {
    //                 $order->order_status = true;
    //                 $order->save(); // Use save() instead of update()
    //                   ActivityLog::create([
    //                 'user_id'   => auth()->id(),
    //                 'action'    => 'Change Status To Unsuccessful',
    //                 'model'     => 'Orders',
    //                 'ip_address' => request()->ip(),
    //                 'user_agent' => request()->header('User-Agent'),
    //             ]);
    //             }
    //         });

    //         $this->dispatch('done', success: "Successfully changed this order status");
    //     } catch (\Throwable $th) {
    //         $this->dispatch('done', error: "Something went wrong: " . $th->getMessage());
    //     }
    // }

    // public function statusToggleReturn($id)
    // {
    //     try {
    //         DB::transaction(function () use ($id) {
    //             $order = Order::findOrFail($id);
                
    //             if (!$order->return_status && !$order->order_status) {
    //                 $order->return_status = true;
    //                 $order->save(); // Use save() instead of update()
    //                   ActivityLog::create([
    //                 'user_id'   => auth()->id(),
    //                 'action'    => 'Returned',
    //                 'model'     => 'Orders',
    //                 'ip_address' => request()->ip(),
    //                 'user_agent' => request()->header('User-Agent'),
    //             ]);
    //             }
    //         });

    //         $this->dispatch('done', success: "Successfully changed this order status");
    //     } catch (\Throwable $th) {
    //         $this->dispatch('done', error: "Something went wrong: " . $th->getMessage());
    //     }
    // }


    public function delete($id)
    {
        try {
            DB::transaction(function () use ($id) {
                $order = Order::findOrFail($id);
                OrderProduct::where('order_id', $id)->delete();
                UnsuccessfulTransaction::where('order_id', $id)->delete();
                $order->delete();
            });

            $this->dispatch('done', success: "Successfully Deleted.");
        } catch (\Throwable $th) {
            $this->dispatch('done', error: "Something went wrong: " . $th->getMessage());
        }
    }

    public function render()
    {
        $search = trim($this->search);

        // ✅ Calculate total_amount in database using subquery
        $orders = Order::query()
            ->select([
                'orders.*',
                DB::raw('COALESCE((
                    SELECT SUM(op.quantity * op.unit_price)
                    FROM order_product op
                    WHERE op.order_id = orders.id
                ), 0) as total_amount')
            ])
            ->leftJoin('customers', 'orders.customer_id', '=', 'customers.id')
            ->with([
                'customer:id,name',
                'products:id,name,barcode'
            ])
            ->when($search, fn($query) =>
                $query->where(function ($sub) use ($search) {
                    $sub->where('orders.created_at', 'like', "%$search%")
                        ->orWhere('customers.name', 'like', "%$search%")
                        ->orWhere('orders.orders_ref', 'like', "%$search%");
                })
            )
            ->orderBy('orders.created_at', 'desc')
            ->simplePaginate(10);

        return view('livewire.admin.orders.index', [
            'orders' => $orders
        ]);
    }
}