<div> <!-- Single root element for Livewire -->
    <x-slot:header>Pending Approvals</x-slot:header>

    <div class="card">
        <div class="card-header bg-inv-primary text-inv-secondary border-0">
            <h5>Approval Center</h5>
        </div>

        <div class="card-body">
            <!-- Tabs -->
            <ul class="nav nav-tabs" role="tablist">
                <li class="nav-item">
                    <button wire:click="setTab('add-products')" class="nav-link {{ $activeTab == 'add-products' ? 'active' : '' }}">New Products</button>
                </li>
                <li class="nav-item">
                    <button wire:click="setTab('returns')" class="nav-link {{ $activeTab == 'returns' ? 'active' : '' }}">Return Requests</button>
                </li>
                <li class="nav-item">
                    <button wire:click="setTab('unsuccessful')" class="nav-link {{ $activeTab == 'unsuccessful' ? 'active' : '' }}">Unsuccessful Transactions</button>
                </li>
                <li class="nav-item">
                    <button wire:click="setTab('edits')" class="nav-link {{ $activeTab == 'edits' ? 'active' : '' }}">Edit Requests</button>
                </li>
            </ul>

            <!-- Tab content -->
            <div class="tab-content mt-3">

                <!-- New Products -->
                <div class="tab-pane fade {{ $activeTab == 'add-products' ? 'show active' : '' }}">
                    @if($pendingAddProducts->isNotEmpty())
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-secondary">
                                <tr>
                                    <th>Date</th>
                                    <th>QTY</th>
                                    <th>Bar Code</th>
                                    <th>Products</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pendingAddProducts as $request)
                                <tr>
                                    <td>{{ $request->created_at->format('Y-m-d') }}</td>
                                    <td>{{ $request->products->sum('pivot.quantity') }}</td>

                                    <td>
                                        @foreach($request->products as $product)
                                        {{ $product->barcode }}<br>
                                        @endforeach
                                    </td>

                                    <td>
                                        @foreach($request->products as $product)
                                        {{ $product->name }} <strong>(x{{ $product->pivot->quantity }})</strong><br>
                                        @endforeach

                                    </td>

                                    <td>
                                        <button wire:click="approve({{ $request->id }}, 'AddProduct')" class="btn btn-success btn-sm">
                                            <i class="bi bi-check-lg"></i> Approve
                                        </button>
                                        <button wire:click="reject({{ $request->id }}, 'AddProduct')" class="btn btn-danger btn-sm">
                                            <i class="bi bi-x-lg"></i> Reject
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="mt-2">
                            {{ $pendingAddProducts->links() }}
                        </div>
                    </div>
                    @else
                    <div class="text-center py-5">
                        <i class="bi bi-inbox fs-1 text-muted mb-3 d-block"></i>
                        <h5 class="text-inv-primary">No Pending New Arrival Record</h5>
                        <p class="text-muted">Start by creating a new arrival with status pending</p>
                    </div>

                    @endif
                </div>

                <!-- Return Requests -->
                <div class="tab-pane fade {{ $activeTab == 'returns' ? 'show active' : '' }}">
                    @if($pendingReturnItem->isNotEmpty())
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-secondary">
                                <tr>
                                    <th>Date</th>
                                    <th>Qty</th>
                                    <th>Barcode</th>
                                    <th>Products</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pendingReturnItem as $request)
                                <tr>
                                    <td>{{ $request->created_at->format('Y-m-d') }}</td>
                                    <td>{{ $request->products->sum('pivot.quantity') }}</td>
                                    <td>
                                        @foreach($request->products as $product)
                                        {{ $product->barcode }}<br>
                                        @endforeach
                                    </td>
                                    <td>
                                        @foreach($request->products as $product)
                                        {{ $product->name }} (x{{ $product->pivot->quantity }})<br>
                                        @endforeach
                                    </td>
                                    <td>
                                        <button wire:click="approve({{ $request->id }}, 'ReturnItem')" class="btn btn-success btn-sm">
                                            <i class="bi bi-check-lg"></i> Approve
                                        </button>
                                        <button wire:click="reject({{ $request->id }}, 'ReturnItem')" class="btn btn-danger btn-sm">
                                            <i class="bi bi-x-lg"></i> Reject
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="mt-2">
                            {{ $pendingReturnItem->links() }}
                        </div>
                    </div>
                    @else
                       <div class="text-center py-5">
                        <i class="bi bi-inbox fs-1 text-muted mb-3 d-block"></i>
                        <h5 class="text-inv-primary">No Pending Return Record</h5>
                        <p class="text-muted">Start by creating a return with status pending</p>
                    </div>
                    @endif
                </div>

                <!-- Unsuccessful Transactions -->
                <div class="tab-pane fade {{ $activeTab == 'unsuccessful' ? 'show active' : '' }}">
                    @if($pendingUnsuccessful->isNotEmpty())
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-secondary">
                                <tr>
                                    <th>Date</th>
                                    <th>Qty</th>
                                    <th>Barcode</th>
                                    <th>Products</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pendingUnsuccessful as $request)
                                <tr>
                                    <td>{{ $request->created_at->format('Y-m-d') }}</td>
                                    <td>{{ $request->products->sum('pivot.quantity') }}</td>
                                    <td>
                                        @foreach($request->products as $product)
                                        {{ $product->barcode }}<br>
                                        @endforeach
                                    </td>
                                    <td>
                                        @foreach($request->products as $product)
                                        {{ $product->name }} (x{{ $product->pivot->quantity }})<br>
                                        @endforeach
                                    </td>
                                    <td>
                                        <button wire:click="approve({{ $request->id }}, 'Unsuccessful')" class="btn btn-success btn-sm">
                                            <i class="bi bi-check-lg"></i> Approve
                                        </button>
                                        <button wire:click="reject({{ $request->id }}, 'Unsuccessful')" class="btn btn-danger btn-sm">
                                            <i class="bi bi-x-lg"></i> Reject
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="mt-2">
                            {{ $pendingUnsuccessful->links() }}
                        </div>
                    </div>
                    @else
                     <div class="text-center py-5">
                        <i class="bi bi-inbox fs-1 text-muted mb-3 d-block"></i>
                        <h5 class="text-inv-primary">No Pending Unsuccessful Transactions Record</h5>
                        <p class="text-muted">Start by creating a unsuccessful transactions with status pending</p>
                    </div>
                    @endif
                </div>

                <!-- Edit Requests -->
                <div class="tab-pane fade {{ $activeTab == 'edits' ? 'show active' : '' }}">
                    @if($pendingEdits->isNotEmpty())
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-secondary">
                                <tr>
                                    <th>Date</th>
                                    <th>Barcode</th>
                                    <th>Products</th>
                                    <th>Requested Changes</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pendingEdits as $edit)
                                <tr>
                                    <td>{{ $edit->created_at->format('Y-m-d') }}</td>
                                    <td>
                                        @foreach($edit->editable->products as $product)
                                        {{ $product->barcode }}<br>
                                        @endforeach
                                    </td>
                                    <td>
                                        @foreach($edit->editable->products as $product)
                                        {{ $product->name }} (x{{ $product->pivot->quantity }})<br>
                                        @endforeach
                                    </td>
                                    <td>
                                        <pre class="bg-light p-2 rounded small mb-0">{{ json_encode($edit->changes, JSON_PRETTY_PRINT) }}</pre>
                                    </td>
                                    <td>
                                        <button wire:click="approve({{ $edit->id }}, 'Edit')" class="btn btn-success btn-sm">
                                            <i class="bi bi-check-lg"></i> Approve
                                        </button>
                                        <button wire:click="reject({{ $edit->id }}, 'Edit')" class="btn btn-danger btn-sm">
                                            <i class="bi bi-x-lg"></i> Reject
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="mt-2">
                            {{ $pendingEdits->links() }}

                        </div>
                    </div>
                    @else
                   <div class="text-center py-5">
                        <i class="bi bi-inbox fs-1 text-muted mb-3 d-block"></i>
                        <h5 class="text-inv-primary">No Edit request</h5>
                    </div>
                    @endif
                </div>

            </div>
        </div>
    </div>
</div>