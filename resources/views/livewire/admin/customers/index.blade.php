<div wire:init="loadData">
    <x-slot name="head">Customers</x-slot>

    <div class="card shadow">

        <x-livewire.partials.new-header-form title="Customers" route="customers" />
        <div class="card-body">
            <x-livewire.partials.search-bar :search="$search" />

            @if($readyToLoad)
            <x-livewire.partials.empty-result-form :var="$customers" msg="Customer" :search="$search">

                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr class="text-center">
                                <th>Basic Details</th>
                                <th>Address</th>
                                <th>Business Registration</th>
                                <th>Organization Type</th>
                                <th>Sales Made</th>
                                <th>Orders Made</th>
                                @if($op)
                                <th>Total Orders Value</th>
                                <th>Total Sales Value</th>
                                @endif
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>

                            @foreach($customers as $customer)
                            @php
                            $count = $counts[$customer->id] ?? null;
                            @endphp
                            <tr class="text-center">
                                <td>
                                    <span>
                                        <h6>{{ $customer->name }}</h6>
                                        <small class="text-muted">{{ $customer->phone_number ?? 'N/A' }}</small>
                                    </span>
                                </td>
                                <td>

                                    <x-livewire.partials.remarks-reason-modal :var="$customer->address" />
                                </td>
                                <td>
                                    <span> <small><strong>TIN ID:</strong> {{ $customer->tax_id ?? 'N/A' }}</small></span>
                                </td>
                                <td>{{ $customer->organization_type ?? 'N/A' }}</td>
                                <td>{{ $count->sales_count ?? 0 }}</td>
                                <td>{{ $count->orders_count ?? 0 }}</td>
                                @if($op)
                                <td>
                                    <span> <small>PISO </small>{{ number_format($count->total_order_amount ?? 0) }}</span>
                                </td>
                                <td>
                                    <span> <small>PISO </small>{{ number_format($count->total_sale_amount ?? 0) }}</span>
                                </td>
                                @endif
                                <td class="text-center">
                                    <x-livewire.partials.edit-delete-form route="customers" id="{{ $customer->id }}" />
                                </td>
                            </tr>
                            @endforeach

                        </tbody>
                    </table>

                    <div class="mt-3">
                        {{ $customers->links() }}
                    </div>
               </x-livewire.partials.empty-result-form>
                        @else
                        <x-livewire.partials.loading-modal-form />

                    @endif
                </div>
        </div>
    </div>
</div>