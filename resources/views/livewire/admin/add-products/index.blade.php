<div wire:init="loadData">
    <x-slot:header>Add Product</x-slot:header>

    <div class="card">
        <x-livewire.partials.new-header-form title="New Arrival" route="add-products" />

        <div class="card-body">
            <div class="mb-3">
                <x-livewire.partials.search-bar :$search />
            </div>

            @if($readyToLoad)

            @if ($search && $addProducts->isEmpty())
            <div class="text-center py-5">
                <i class="bi bi-search fs-1 text-muted mb-3 d-block"></i>
                <h5 class="text-inv-primary">No New Arrival found for "{{ $search }}"</h5>
                <p class="text-muted">Try adjusting your search terms</p>
            </div>
            @elseif(!$search && $addProducts->isEmpty())
            <div class="text-center py-5">
                <i class="bi bi-inbox fs-1 text-muted mb-3 d-block"></i>
                <h5 class="text-inv-primary">No New Arrival Record</h5>
                <p class="text-muted">Start by creating a new arrival's</p>
            </div>

            @else
            <div class="table-responsive">

                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr class="text-center">
                            <th>Added Date</th>
                            <th>BarCode</th>
                            <th>Product</th>
                            <th>Quantity Added</th>
                            <th>Approval Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>

                        @foreach ($addProducts as $addProduct)
                        <tr class="text-center">

                            <td>
                                <span>{{ \Carbon\Carbon::parse($addProduct->created_at)->format('M j, Y') }}</span>
                            </td>

                            <td>
                                @if($addProduct->products->count()>2)
                                <x-livewire.partials.product-barcode-modal :var="$addProduct" method="barcode" />
                                @else
                                <span>{{ $addProduct->products->pluck('barcode')->filter()->join(', ') ?: 'N/A' }}</span>
                                @endif
                            </td>

                            <td>
                                @if($addProduct->products->count()>2)
                                <x-livewire.partials.product-barcode-modal :var="$addProduct" method="name" />
                                @else
                                <span>{{ $addProduct->products->pluck('name')->filter()->join(', ') ?: 'N/A' }}</span>
                                @endif
                            </td>

                            <td><strong>{{ rtrim(rtrim(number_format($addProduct->total_quantity ?? 0, 2), '0'), '.') }}</strong></td>

                            <td>
                                <span class="badge {{ in_array($addProduct->status, ['pending', 'rejected']) ? 'bg-danger' : 'bg-success' }}">
                                    <a
                                        href="{{ route('admin.approvals.approvalcenter') }}"
                                        class="text-white text-decoration-none fw-bold">
                                        {{ strtoupper($addProduct->status) }}
                                    </a>
                                </span>
                            </td>


                            <td>
                                <x-livewire.partials.unapprove-delete-form route="add-products" id="{{$addProduct->id}}" :var="$addProduct" />
                            </td>

                        </tr>
                        @endforeach

                    </tbody>
                </table>

                <div class="mt-3">
                    {{ $addProducts->links() }}
                </div>
                @endif

                @else
                <x-livewire.partials.loading-modal-form />
                @endif

            </div>
        </div>
    </div>
</div>