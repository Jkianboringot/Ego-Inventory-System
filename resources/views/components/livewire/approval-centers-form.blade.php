 @props(['messeage','id','pendingVar','param'])
 <div class="tab-pane fade" id="{{ $id }}" role="tabpanel">
                    @if($pendingVar->isNotEmpty())
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-secondary">
                                    <tr>
                                        <th>Date</th>
                                        <th>No. of Units</th>
                                        <th>Product</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($pendingVar as $request)
                                        <tr>
                                            <td>{{ $request->created_at->format('Y-m-d') }}</td>
                                            <td>{{ $request->products->sum('pivot.quantity') }}</td>
                                            <td>
                                                @foreach($request->products as $product)
                                                    {{ $product->name }} (x{{ $product->pivot->quantity }})<br>
                                                @endforeach
                                            </td>
                                            <td>
                                                <button wire:click="approve({{ $request->id }}, '{{$param}}')" class="btn btn-success btn-sm">
                                                    <i class="bi bi-check-lg"></i> Approve
                                                </button>
                                                <button wire:click="reject({{ $request->id }}, '{{$param}}')" class="btn btn-danger btn-sm">
                                                    <i class="bi bi-x-lg"></i> Reject
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted">No pending {{ $messeage }}.</p>
                    @endif
                </div>