<div wire:init="loadData">
    <x-slot:header>Add Product</x-slot:header>

    <div class="card shadow">

        <x-livewire.partials.new-header-form title="Unsuccessfull Transactions" route="unsuccessful-transactions" />
        <div class="card-body ">
            <x-livewire.partials.search-bar :$search />
            @if($readyToLoad)
            @if ($search && $unsuccessfulTransactions->isEmpty())

            <div class="text-center py-5">
                <i class="bi bi-search fs-1 text-muted mb-3 d-block"></i>
                <h5 class="text-inv-primary">No Unsuccessful Transaction found for "{{ $search }}"</h5>
                <p class="text-muted">Try adjusting your search terms</p>
            </div>
            @elseif ($unsuccessfulTransactions->isEmpty())
            <div class="text-center py-5">
                <i class="bi bi-inbox fs-1 text-muted mb-3 d-block"></i>
                <h5 class="text-inv-primary">No Unsuccessful Transaction Record</h5>
                <p class="text-muted">Start by creating a unsuccessful transaction</p>
            </div>
            @else
            <div class="table-responsive">

                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr class="text-center">

                            <th>Product Added Date</th>
                            <th>BarCode</th>
                            <th>Product</th>

                            <th>Quantity</th>
                            <th>Approval Status</th>


                            <th>Actions</th>



                        </tr>
                    </thead>
                    <tbody>

                        @foreach ($unsuccessfulTransactions as $unsuccessfulTransaction)
                        <tr class="text-center">

                            <td>
                                {{ \Carbon\Carbon::parse($unsuccessfulTransaction->created_at)->format('jS F, Y') }}
                            </td>
                            <td>
                                @if($unsuccessfulTransaction->products->count()>2)
                                <x-livewire.partials.product-barcode-modal :var="$unsuccessfulTransaction" method="barcode" />
                                @else
                                {{ $unsuccessfulTransaction->products->pluck('barcode')->filter()->join(', ') ?: 'N/A' }}
                                @endif
                            </td>
                            <td>
                                @if($unsuccessfulTransaction->products->count()>2)
                                <x-livewire.partials.product-barcode-modal :var="$unsuccessfulTransaction" method="name" />
                                @else
                                {{ $unsuccessfulTransaction->products->pluck('name')->filter()->join(', ') ?: 'N/A' }}
                                @endif
                            </td>
                            <td><strong>{{ rtrim(rtrim(number_format($unsuccessfulTransaction->total_quantity ?? 0, 2), '0'), '.') }}</strong></td>


                     
                            
                            <td>
                                <span class="badge {{ in_array($unsuccessfulTransaction->status, ['pending', 'rejected']) ? 'bg-danger' : 'bg-success' }} text-white fw-bold">
                                    {{ strtoupper($unsuccessfulTransaction->status) }}
                                </span>
                            </td>

                            <td>

                                <x-livewire.partials.unapprove-delete-form route="unsuccessful-transactions" id="{{$unsuccessfulTransaction->id}}" :var="$unsuccessfulTransaction" />

                            </td>

                        </tr>
                        @endforeach
                        <tr class="text-center">
                            <td><strong>TOTAL QUANTITY</strong></td>
                            <td></td>
                            <td></td>

                            <td><strong>{{ number_format($unsuccessfulTransactions->sum('total_quantity')) }}</strong></td>
                            <td></td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>


                <div class="mt-3">
                    {{ $unsuccessfulTransactions->links() }}

                </div>
                @endif

                @else
                <x-livewire.partials.loading-modal-form />


                @endif

            </div>
        </div>
    </div>
</div>