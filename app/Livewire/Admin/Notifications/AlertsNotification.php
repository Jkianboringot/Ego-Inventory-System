<?php

namespace App\Livewire\Admin\Notifications;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\Paginator;

class AlertsNotification extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';
    
    public $perPage = 10;
    public $searchTerm = '';

    public function updatingSearchTerm()
    {
        $this->resetPage();
    }

    public function refreshAlerts()
    {
        Cache::forget('low_stock_products');
        $this->dispatch('alert', message: 'Alerts refreshed!');
    }

    public function render()
    {
        $lowStockProducts = Cache::remember('low_stock_products', 300, function () {
            return DB::select("
                SELECT * FROM (
                    SELECT 
                        p.id,
                        p.name,
                        p.inventory_threshold,
                        p.sale_price,
                        GREATEST(
                            COALESCE((
                                SELECT SUM(apl.quantity)
                                FROM add_products_to_list apl
                                JOIN add_products ap ON ap.id = apl.add_product_id
                                WHERE apl.product_id = p.id
                                AND ap.status = 'approved'
                                AND ap.deleted_at IS NULL
                            ), 0)
                            + COALESCE((
                                SELECT SUM(utl.quantity)
                                FROM unsuccessful_transactions_to_list utl
                                JOIN unsuccessful_transactions ut ON ut.id = utl.unsuccessful_transaction_id
                                WHERE utl.product_id = p.id
                                AND ut.status = 'approved'
                            ), 0)
                            + COALESCE((
                                SELECT SUM(pr.quantity)
                                FROM product_return pr
                                JOIN return_items ri ON ri.id = pr.return_item_id
                                WHERE pr.product_id = p.id
                                AND ri.status = 'approved'
                                AND ri.deleted_at IS NULL
                            ), 0)
                            - COALESCE((
                                SELECT SUM(ps.quantity)
                                FROM product_sale ps
                                JOIN sales s ON s.id = ps.sale_id
                                WHERE ps.product_id = p.id
                                AND s.deleted_at IS NULL
                            ), 0)
                            - COALESCE((
                                SELECT SUM(op.quantity)
                                FROM order_product op
                                JOIN orders o ON o.id = op.order_id
                                WHERE op.product_id = p.id
                                AND o.deleted_at IS NULL
                            ), 0),
                        0) as inventory_balance
                    FROM products p
                    WHERE p.deleted_at IS NULL
                ) AS subquery
                WHERE inventory_balance <= COALESCE(inventory_threshold, 10)
            ");
        });

        $filtered = collect($lowStockProducts)
            ->when($this->searchTerm, fn($c) => 
                $c->filter(fn($p) => stripos($p->name, $this->searchTerm) !== false)
            );

        $currentPage = Paginator::resolveCurrentPage() ?: 1;
        $paginated = new Paginator(
            $filtered->forPage($currentPage, $this->perPage),
            $this->perPage,
            $currentPage,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return view('components.alerts-notification', [
            'lowStockProducts' => $paginated
        ]);
    }
}