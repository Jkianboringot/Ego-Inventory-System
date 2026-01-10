<div wire:init="loadData">
    <x-slot:header>{{ $title }}</x-slot:header>

    <div class="card shadow">
        <x-livewire.partials.new-header-form title="{{$title}}" route="purchases" />


        <div class="card-body">
            <x-livewire.partials.search-bar :$search />

            @if($readyToLoad)
            @if ($search && $purchases->isEmpty())
            <div class="text-center py-5">
                <i class="bi bi-search display-4 text-muted"></i>
                <p class="text-muted mt-3">No Purchase found for "<strong>{{ $search }}</strong>"</p>
            </div>
            @elseif ($purchases->isEmpty())
            <div class="text-center py-5">
                <i class="bi bi-box-seam display-4 text-muted"></i>
                <p class="text-muted mt-3">No Purchase available</p>
            </div>
            @else
            <div class="table-responsive">

                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr class="text-center">

                            <th>Purchase Date</th>
                            <th>Supplier</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Date Settled</th>
                            <th>Actions</th>



                            <!-- how about i make it check if it has a edit or delete only then is action permision allowed -->
                        </tr>
                    </thead>
                    <tbody>


                        @foreach ($purchases as $purchase)
                        <tr class="text-center">

                            <td>
                                <h6>{{Carbon\Carbon::parse($purchase->created_at)->format('jS F,Y') }}</h6>
                            </td>
                            <td>
                                {{ $purchase->supplier->name??"N/A"}}
                            </td>
                            <td>
                                <span> <strong>{{ rtrim(rtrim(number_format($purchase->total_amount ?? 0, 2), '0'), '.')  }}</strong>
                                </span>
                            </td>
                            <td>
                                <div class="d-flex flex-column align-items-center">

                                    <span class="{{ in_array($purchase->is_paid, ['Paid', 'Partially Paid']) ? 'badge bg-success' : 'badge bg-danger' }}" style="font-weight: bold;">
                                        {{ in_array($purchase->is_paid, ['Paid', 'Partially Paid']) ? $purchase->is_paid : ($purchase->is_paid??'N/A') }}
                                    </span>
                                </div>

                            </td>
                            <td>{{ $purchase->date_settled??'N/A' }}</td>
                            <td>

                                @if(!in_array($purchase->is_paid, ['Paid', 'Partially Paid']))
                                <button class="btn btn-warning"
                                    wire:confirm="Are you sure to set this Record to Paid"
                                    wire:click='isPaid({{ $purchase->id }})'>
                                    <i class="bi bi-currency-dollar"></i>
                                </button>
                                @endif
                                <x-livewire.partials.edit-delete-form route="purchases" id="{{$purchase->id}}" />

                            </td>

                        </tr>
                        @endforeach
                        <tr class="text-center">
                            <td><strong>TOTALS</strong></td>
                            <td></td>
                            <td>
                            <span> <strong>{{ rtrim(rtrim(number_format($purchases->sum('total_amount')  ?? 0, 2), '0'), '.')  }}</strong>
</td>
                                <td></td>
                                <td></td>

                        </tr>
                    </tbody>
                </table>
                @endif

                <div class="mt-3">
                    {{ $purchases->links() }}

                </div>
                @else
                <x-livewire.partials.loading-modal-form />


                @endif
            </div>
        </div>
    </div>
</div>