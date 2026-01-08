<div wire:init="loadData">
    <x-slot:header>Returns</x-slot:header>


    <div class="card">
        <x-livewire.partials.new-header-form title="Returns" route="returns" />

        <div class="card-body">

            <x-livewire.partials.search-bar :$search />
            @if($readyToLoad)

            @if ($search && $returns->isEmpty())
            <div class="text-center py-5">
                <i class="bi bi-search fs-1 text-muted mb-3 d-block"></i>
                <h5 class="text-inv-primary">No Return record found for "{{ $search }}"</h5>
                <p class="text-muted">Try adjusting your search terms</p>
            </div>

            @elseif ($returns->isEmpty())

            <div class="text-center py-5">
                <i class="bi bi-inbox fs-1 text-muted mb-3 d-block"></i>
                <h5 class="text-inv-primary">No Return Record</h5>
                <p class="text-muted">Start by creating a return's</p>
            </div>
            @else
            <div class="table-responsive">

                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr class="text-center">
                            <th>Return Type</th>
                            <th>Date</th>
                            <th>BarCode</th>
                            <th>Products</th>
                            <th>Reason</th>
                            <th>Status</th>
                            <th>Refunded Amount</th>
                            <th>Adds On Amount</th>
                            <th>Total Qty</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>

                        @foreach ($returns as $return)
                        <tr class="text-center">
                            <td class="text-center align-middle">
                                <span
                                    class="badge  px-3 py-2 fw-bold d-inline-flex align-items-center justify-content-center
        {{ $return->return_type === 'refunded' ? 'bg-danger' : 'bg-success' }}"
                                    style="width: 120px;">
                                    {{ strtoupper($return->return_type) }}
                                </span>
                            </td>



                            <td>{{ \Carbon\Carbon::parse($return->created_at)->format('jS F, Y') }}</td>
                            <td>
                                @if($return->products->count() > 2)
                                <x-livewire.partials.product-barcode-modal :var='$return' method="barcode" />
                                @else
                                {{ $return->products->pluck('barcode')->filter()->join(', ') ?: 'N/A' }}
                                @endif
                            </td>
                            <td>
                                @if($return->products->count() > 2)
                                <x-livewire.partials.product-barcode-modal :var='$return' method="name" />
                                @else
                                {{ $return->products->pluck('name')->filter()->join(', ') ?: 'N/A' }}
                                @endif
                            </td>
                            <td>

                                <x-livewire.partials.remarks-reason-modal :var="$return->reason" />

                            </td>


                            <td>
                                <span class="badge {{ in_array($return->status, ['pending', 'rejected']) ? 'bg-danger' : 'bg-success' }}">
                                    <a
                                        href="{{ route('admin.approvals.approvalcenter') }}"
                                        class="text-white text-decoration-none fw-bold">
                                        {{ strtoupper($return->status) }}
                                    </a>
                                </span>
                            </td>
                            <td> <strong>{{ rtrim(rtrim(number_format($return->total_amount ?? 0, 2), '0'), '.')  }}</strong>
                            </td>
                            <td> {{ rtrim(rtrim(number_format($return->adds_on ?? 0, 2), '0'), '.') }} </strong></td>

                            <td><strong>{{ rtrim(rtrim(number_format($return->total_quantity ?? 0, 2), '0'), '.') }}</strong></td>

                            <td class="text-center">
                                <x-livewire.partials.unapprove-delete-form route="returns" id="{{ $return->id }}" :var="$return" />
                            </td>

                        </tr>
                        @endforeach



                    </tbody>
                </table>

                <div class="mt-3">
                    {{ $returns->links() }}
                </div>

                @endif

                @else
                <x-livewire.partials.loading-modal-form />


                @endif
            </div>
        </div>
    </div>