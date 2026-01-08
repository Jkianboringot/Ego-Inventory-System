<div wire:init="loadData">
    <x-slot:header>Sales</x-slot:header>
    <div class="card shadow">

        <x-livewire.partials.new-header-form title="Sales" route="sales" />

        <div class="card-body">
            <x-livewire.partials.search-bar :$search />
            @if($readyToLoad)
            @if ($search && $sales->isEmpty())
            <div class="text-center py-5">
                <i class="bi bi-search fs-1 text-muted mb-3 d-block"></i>
                <h5 class="text-inv-primary">No Sale record found for "{{ $search }}"</h5>
                <p class="text-muted">Try adjusting your search terms</p>
            </div>


            @elseif ($sales->isEmpty())
            <div class="text-center py-5">
                <i class="bi bi-inbox fs-1 text-muted mb-3 d-block"></i>
                <h5 class="text-inv-primary">No Sale Record</h5>
                <p class="text-muted">Start by creating a sale's</p>
            </div>
            @else
            <div class="table-responsive">

                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr class="text-center">
                            <th>Sale Ref</th>
                            <th>Sale Date</th>
                            <th>Customer</th>

                            <th>Bar Code</th>
                            <th>Product</th>
                            <!-- <th>Return Status</th> -->
                            <th>Qty Sold</th>


                            @if ($op)

                            <th>Total Amount</th>
                            @endif
                            <!-- i will keep this like this for now since edit and create are property only admin has -->

                            <th>Actions</th>


                        </tr>
                    </thead>
                    <tbody>


                        @foreach ($sales as $sale)
                        <tr class="text-center">
                            <td><small>{{ $sale->sales_ref }}</small></td>

                            <td>
                                {{Carbon\Carbon::parse($sale->created_at)->format('jS F,Y') }}
                            </td>
                            <td>{{ $sale->customer->name??'N/A' }}</td>

                            <td>
                                @if($sale->products->count()>2)
                                <x-livewire.partials.product-barcode-modal :var='$sale' method="barcode" />
                                @else
                                {{ $sale->products->pluck('barcode')->filter()->join(', ') ?: 'N/A' }}
                                @endif
                            </td>
                            <td>
                                @if($sale->products->count()>2)
                                <x-livewire.partials.product-barcode-modal :var='$sale' method="name" />
                                @else
                                {{ $sale->products->pluck('name')->filter()->join(', ') ?: 'N/A' }}
                                @endif
                            </td>
                             {{-- <td class="text-center">
                                <div class="d-flex justify-content-center">
                                    <span
                                        class="badge btn-badge-toggle {{ $sale->return_status ? 'bg-danger' : 'bg-success' }}">
                                        <i class="bi bi-exclamation-triangle me-1"></i>
                                          {{ $sale->return_status ? 'Returned' : 'Not Returned' }}

                                    </span>
                                </div>
                            </td>
                             --}}

                            <!-- ok this new decrease the query by 20 from 47 to 27 -->

                            <td><strong>{{ rtrim(rtrim(number_format($sale->total_quantity ?? 0, 2), '0'), '.') }}</strong></td>

                            @if ($op)

                            <td><span>
                                    <strong>{{ rtrim(rtrim(number_format($sale->total_amount ?? 0, 2), '0'), '.')  }}</strong>
                                </span></td>
                            @endif



                            <td class="text-center">

                                <x-livewire.partials.edit-delete-form route="sales" id="{{$sale->id}}"  />

                            </td>
                        </tr>
                        @endforeach
                        @if ($op)

                        <tr class="text-center">
                            <td><strong>TOTALS</strong></td>
                            <td colspan="3"></td>
                            <td></td>

                            <td><strong>{{ $pageTotals['quantity']  }}</strong></td>

                            <td>
                                <span> <strong>{{ rtrim(rtrim(number_format($pageTotals['amount']  ?? 0, 2), '0'), '.')  }}</strong>
                                </span>
                            </td>




                            <td></td>

                        </tr>

                        @endif
                    </tbody>
                </table>

                <div class="mt-3">
                    {{ $sales->links() }}

                </div>
                @endif

                @else
                <x-livewire.partials.loading-modal-form />


                @endif
            </div>
        </div>
    </div>
</div>