<div wire:init="loadData">

    <x-slot:head>Suppliers</x-slot:head>

    <div class="card shadow">

        <x-livewire.partials.new-header-form title="Suppliers" route="suppliers" />
        <div class="card-body">
            <x-livewire.partials.search-bar :$search />

            <div class="table-responsive">

                @if($readyToLoad)
                <x-livewire.partials.empty-result-form :var="$suppliers" msg="Supplier" :search="$search">

                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr class="text-center">
                                <th>Supplier Name</th>
                                <th>Address</th>
                                <th>Business Details</th>
                                <th>Accounts Number</th>
                                <th>Purchases Made</th>
                                <th>Total Purchases Value</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>

                            @foreach($suppliers as $supplier)
                            <tr class="text-center">
                                <td>
                                    <h6>{{ $supplier->name }}</h6>
                                    <small class="text-muted">{{ $supplier->phone_number ?? 'N/A' }}</small>
                                </td>
                                <td>
                                    <x-livewire.partials.remarks-reason-modal :var="$supplier->address" />
                                </td>
                                <td>
                                    <p> <small class="text-muted">Tax ID:</small> {{$supplier->tax_id ?? 'N/A' }}</p>

                                </td>
                                <td>
                                    <p> <small class="text-muted">A/c NO:</small> {{ $supplier->account_number ?? 'N/A' }}</p>
                                </td>
                                <td><strong>{{ $supplier->purchase_count ?? 0 }}</strong></td>
                                <td><small>PISO </small><strong>{{ number_format($supplier->total_amount ?? 0, 2) }}</strong></td>
                                <td class="text-center">
                                    <x-livewire.partials.edit-delete-form route="suppliers" id="{{$supplier->id}}" />
                                </td>
                            </tr>
                            @endforeach
                      
                        </tbody>
                    </table>


                    <div class="mt-3">
                        {{ $suppliers->links() }}

                    </div>
                  </x-livewire.partials.empty-result-form>
                    @else
                    <x-livewire.partials.loading-modal-form />


                    @endif
            </div>
        </div>
    </div>
</div>