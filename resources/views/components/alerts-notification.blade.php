<div >

<div class="card-header-dark border-3 border-inv-secondary  d-flex align-items-center ">
        <h5 class="text-white mb-0 d-flex align-items-center">
            <i class="bi bi-bell-fill me-2 text-blue"></i>
            Low Stock Alerts
        </h5>

        <div class="ms-auto">
            <button 
                wire:click="refreshAlerts" 
                class="btn-inv-secondary text-black btn-outline-secondary d-flex align-items-center gap-1"
            >
                <i class="bi bi-arrow-clockwise"></i>
                Refresh
            </button>
        </div>
    </div>

    <div class="card-body p-3 bg-inv-primary rounded-bottom">

        <div class="mb-3">
            <input 
                type="text" 
                class="form-control form-control-sm"
                placeholder="Search product..."
                wire:model.live.debounce.300ms="searchTerm"
            >
        </div>

        @if ($lowStockProducts->isEmpty())
            <div class="text-center py-5 text-muted">
                <i class="bi bi-check-circle fs-1 mb-2"></i>
                <p class="mb-0">No low stock products.</p>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-sm table-hover align-middle mb-0">
                    <thead class="text-inv-primary">
                        <tr>
                            <th>Product Name</th>
                            <th class="text-center">Stock</th>
                            <th class="text-center">Threshold</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($lowStockProducts as $index => $product)
                        <tr>
                            <td><strong>{{ $product->name }}</strong></td>
                            <td class="text-center">
                                <span class="badge bg-danger">{{ number_format($product->inventory_balance) }}</span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-secondary">{{ $product->inventory_threshold ?? 10 }}</span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $lowStockProducts->links() }}
            </div>
        @endif
    </div>
</div>
