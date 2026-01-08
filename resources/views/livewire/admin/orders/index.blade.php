<div wire:init="loadData">
    <x-slot:header>Orders</x-slot:header>

    @php
    $user = auth()->user();
    @endphp
    <div class="card shadow">

        <x-livewire.partials.new-header-form title="Orders" route="orders" />

        <div class="card-body ">
            <x-livewire.partials.search-bar :$search />
            @if($readyToLoad)
            @if ($search && $orders->isEmpty())
            <tr class="text-center">
                <div class="text-center py-5">
                    <i class="bi bi-search fs-1 text-muted mb-3 d-block"></i>
                    <h5 class="text-inv-primary">No Order record found for "{{ $search }}"</h5>
                    <p class="text-muted">Try adjusting your search terms</p>
                </div>

            </tr>
            @elseif ($orders->isEmpty())
            <div class="text-center py-5">
                <i class="bi bi-inbox fs-1 text-muted mb-3 d-block"></i>
                <h5 class="text-inv-primary">No Order Record</h5>
                <p class="text-muted">Start by creating a order's</p>
            </div>
            @else
            <div class="table-responsive">

                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr class="text-center">
                            <th>Order Ref</th>
                            <th>Order Date</th>
                            <th>Customer</th>
                            <th>BarCode</th>
                            <th>Product</th>
                            <!-- <th>Transaction Status</th> -->
                            <!-- <th>Return Status</th> -->
                            <th>Total Amount</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>

                        @foreach ($orders as $order)
                        <tr class="text-center">
                            <td><small>{{ $order->orders_ref }}</small></td>
                            <td>
                                {{ Carbon\Carbon::parse($order->created_at)->format('jS F,Y') }}
                            </td>
                            <td>
                                {{ $order->customer->name ?? 'N/A' }}
                            </td>
                            <td>
                                @if($order->products->count() > 2)
                                <x-livewire.partials.product-barcode-modal :var='$order' method="barcode" />
                                @else
                                {{ $order->products->pluck('barcode')->filter()->join(', ') ?: 'N/A' }}
                                @endif
                            </td>
                            <td>
                                @if($order->products->count() > 2)
                                <x-livewire.partials.product-barcode-modal :var='$order' method="name" />
                                @else
                                {{ $order->products->pluck('name')->filter()->join(', ') ?: 'N/A' }}
                                @endif
                            </td>


                            <!-- <td class="text-center">
                                <div class="d-flex justify-content-center">
                                    <span
                                        class="badge btn-badge-toggle {{ $order->order_status ? 'bg-danger' : 'bg-success' }}">
                                        <i class="bi bi-exclamation-triangle me-1"></i>
                                        {{ $order->order_status ? 'Unsuccessful' : 'Successful' }}

                                    </span>
                                </div>
                            </td> -->

                                {{-- <td class="text-center">
                                <div class="d-flex justify-content-center">
                                    <span
                                        class="badge btn-badge-toggle {{ $order->return_status ? 'bg-danger' : 'bg-success' }}">
                                        <i class="bi bi-exclamation-triangle me-1"></i>
                                          {{ $order->return_status ? 'Returned' : 'Not Returned' }}

                                    </span>
                                </div>
                            </td>
                             --}}




                            <td> <strong>{{ rtrim(rtrim(number_format($order->total_amount ?? 0, 2), '0'), '.')  }}</strong>
                            </td>

                            <td class="text-center">

                                <x-livewire.partials.edit-delete-form route="orders" id="{{$order->id}}" />




                            </td>
                        </tr>
                        @endforeach


                    </tbody>
                </table>

                <div class="mt-3">
                    {{ $orders->links() }}
                </div>
                @endif

                @else
                <x-livewire.partials.loading-modal-form />


                @endif
            </div>
        </div>
    </div>
</div>