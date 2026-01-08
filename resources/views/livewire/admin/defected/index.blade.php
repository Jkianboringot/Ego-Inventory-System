<div wire:init="loadData">
    <x-slot:header>Add Product</x-slot:header>


    <div class="card shadow">
        <x-livewire.partials.new-header-form title="Defected Products" route="defected" />
        <div class="card-body">

            <x-livewire.partials.search-bar :$search />

            <div class="table-responsive">

                @if($readyToLoad)
                @if ($search && $defects->isEmpty())
               <div class="text-center py-5">
                <i class="bi bi-search fs-1 text-muted mb-3 d-block"></i>
                <h5 class="text-inv-primary">No Defected record found for "{{ $search }}"</h5>
                <p class="text-muted">Try adjusting your search terms</p>
            </div>

                @elseif ($defects->isEmpty())
                
<div class="text-center py-5">
                <i class="bi bi-inbox fs-1 text-muted mb-3 d-block"></i>
                <h5 class="text-inv-primary">No Defected Record</h5>
                <p class="text-muted">Start by creating a defected record's</p>
            </div>

                @else
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr class="text-center">
                            <th>Date</th>
                            <th>Barcode</th>
                            <th>Product</th>
                            <th>Reason</th>
                            <th>Total Qty</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($defects as $defect)
                        <tr class="text-center">
                            <td>{{ \Carbon\Carbon::parse($defect->created_at)->format('jS F, Y') }}</td>

                            <td>
                                @if($defect->products->count()>2)
                                <x-livewire.partials.product-barcode-modal :var="$defect" method="barcode" />
                                @else
                                {{ $defect->products->pluck('barcode')->filter()->join(', ') ?: 'N/A' }}
                                @endif
                            </td>
                            <td>
                                @if($defect->products->count()>2)
                                <x-livewire.partials.product-barcode-modal :var="$defect" method="name" />
                                @else
                                {{ $defect->products->pluck('name')->filter()->join(', ') ?: 'N/A' }}
                                @endif
                            </td>
                            <td>
                                <x-livewire.partials.remarks-reason-modal :var="$defect->remarks" />


                            </td>
                            <td><strong>{{ rtrim(rtrim(number_format($defect->total_quantity ?? 0, 2), '0'), '.') }}</strong></td>

                            <td>
                                <x-livewire.partials.edit-delete-form route="defected" id="{{$defect->id}}" />
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @endif


                <div class="mt-3">
                    {{ $defects->links() }}

                </div>
                @else
                <x-livewire.partials.loading-modal-form />


                @endif
            </div>
        </div>
    </div>
</div>