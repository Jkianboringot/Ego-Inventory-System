<?php

namespace App\Livewire\Admin\ActivityLogs;

use App\Models\Product;
use App\Traits\LoadData;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\ActivityLog;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class Index extends Component
{
    use WithPagination;
    use LoadData;

    public $search = '';
    protected $paginationTheme = 'bootstrap';


    public function render()
    {
        $search = trim($this->search);

        $logs = ActivityLog::with('user:id,name')
            ->when($search, fn($query) =>
                $query->where(function ($sub) use ($search) {
                    $sub->where('action', 'like', "%$search%")
                        ->orWhere('model', 'like', "%$search%")
                        ->orWhere('created_at', 'like', "%$search%");
                }))
            ->latest('created_at')
            ->simplePaginate(10);

        // Enrich logs with product names
        $this->enrichLogsWithProductNames($logs);

        return view('livewire.admin.activity-logs.index', [
            'logs' => $logs
        ]);
    }

    private function enrichLogsWithProductNames($logs)
    {
        // Collect all product IDs from all logs
        $productIds = [];
        
        foreach ($logs as $log) {
            if ($log->changes) {
                $changes = json_decode($log->changes, true);
                
                if (is_array($changes) && isset($changes['products']) && is_array($changes['products'])) {
                    foreach ($changes['products'] as $product) {
                        if (isset($product['product_id'])) {
                            $productIds[] = $product['product_id'];
                        }
                    }
                }
            }
        }

        // Fetch all products in one query if there are any IDs
        if (!empty($productIds)) {
            $products = Product::whereIn('id', array_unique($productIds))
                ->get()
                ->keyBy('id');

            // Enrich each log's changes with product names
            foreach ($logs as $log) {
                if ($log->changes) {
                    $changes = json_decode($log->changes, true);
                    
                    if (is_array($changes) && isset($changes['products']) && is_array($changes['products'])) {
                        foreach ($changes['products'] as $key => $product) {
                            if (isset($product['product_id']) && !isset($product['product_name'])) {
                                $productModel = $products->get($product['product_id']);
                                $changes['products'][$key]['product_name'] = $productModel 
                                    ? $productModel->name 
                                    : 'Product #' . $product['product_id'];
                            }
                        }
                        
                        // Update the changes attribute
                        $log->enriched_changes = $changes;
                    } else {
                        // Set enriched_changes to original if not a product change
                        $log->enriched_changes = $changes;
                    }
                } else {
                    // Set enriched_changes to null if no changes
                    $log->enriched_changes = null;
                }
            }
        } else {
            // If no product IDs, just set enriched_changes to original changes
            foreach ($logs as $log) {
                $log->enriched_changes = $log->changes ? json_decode($log->changes, true) : null;
            }
        }
    }
}