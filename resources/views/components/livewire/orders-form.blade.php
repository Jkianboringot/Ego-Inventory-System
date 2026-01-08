@props(['customerSearch',
'productSearch',
'customers',
'products',
'productList',
'order',
'selectedProductId',
'productCache' => []
])

<div>
    <x-livewire.partials.cancel-buttons />
    <x-slot:header>Order</x-slot:header>

    @if (session()->has('warning'))
    <div class="alert alert-warning d-flex justify-content-between align-items-center">
        <span>{{ session('warning') }}</span>
        <button wire:click="continueAnyway" class="btn btn-sm btn-outline-danger ms-2">
            Continue Anyway
        </button>
    </div>
    @endif

    <div class="row justify-content-center">
        {{-- LEFT SIDE --}}

        <div class="col-md-4 col-6">
            <form wire:submit.prevent="addToList">

                <div class="card">
                    <div class="card-header bg-inv-primary text-inv-secondary border-0">
                        <h6 class="text-center">Set Date & Supplier</h6>
                    </div>
                    <div class="card-body">

                        <div class="mb-3">
                            <label for="search" class="form-label">Customer Search</label>
                            <input type="text" id='search' wire:model.live='customerSearch' class="form-control" placeholder="Type customer's name (optional)" />
                            @error('order.customer_id')
                            <small id="helpId" class="form-text text-danger">{{ $message }}</small>
                            @enderror
                            <ul class="list-group mt-2 w-100">
                                @if ($customerSearch != '')
                                @foreach ($customers as $customer)
                                <x-customer-list-item :customer="$customer" :category="$order" type='order' />
                                @endforeach
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="card mt-2">
                    <div class="card-header bg-inv-primary text-inv-secondary border-0">
                        <h6 class="text-center">Add Ordered Product's</h6>
                    </div>
                    <div class="card-body ">
                        <x-livewire.partials.product-search :$products :$selectedProductId :$productSearch />

                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="q" class="form-label">Quantity</label>
                                <input id='q' wire:model.defer='quantity' type="number" min="0.01" step="0.01" max="999999.99" class="form-control"
                                    placeholder="Enter quantity" required />
                                @error('quantity')
                                <small id="helpId" class="form-text text-danger">{{ $message }} </small>
                                @enderror
                            </div>
                        </div>


                        <x-livewire.partials.add-to-list-buttons />
                    </div>
                </div>
            </form>
        </div>

        {{-- RIGHT SIDE (CART) --}}
        <div class="col-md-8 col-6">
            <div class="card shadow" id="cart-section">
                <div class="card-header text-inv-primary border-0">
                    <h5 class="text-center text-uppercase">Ordered Products</h5>
                </div>
                <div class="card-body">
                    @if ($productList && count($productList) > 0)
                    <table class="table" style="table-layout: fixed;">
                        <thead>
                            <tr>

                                <th>Product </th>
                                <th class="text-center">Quantity</th>
                                <th class="text-center">Selling Price</th>
                                <th class="text-center">Total</th>

                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $total = 0; @endphp
                            @foreach ($productList as $key => $listItem)
                            <tr>

                                {{-- ✅ Use cached product name instead of querying --}}
                                <td>
                                    @php
                                    $productId = $listItem['product_id'];
                                    $productName = $productCache[$productId]['name'] ?? null;
                                    @endphp
                                    {{ $productName ?? 'Loading...' }}
                                </td>

                                <td>
                                    <x-livewire.partials.quantity-input :$key />
                                </td>
                                <td>PISO {{ number_format($listItem['price'], 2) }}</td>

                                <td>PISO {{ number_format($listItem['quantity'] * $listItem['price'], 2) }}</td>

                                


                                <td class="text-center">
                                    <x-livewire.partials.add-sub-delete-form :listItem="$listItem" :key="$key" />

                                </td>
                            </tr>
                            @endforeach


                        </tbody>
                    </table>
                    <form wire:submit.prevent="save">

                        <div class="d-flex justify-content-end gap-2 mt-3">
                            <x-livewire.partials.cancelProcess-button />
                            <x-livewire.partials.save-buttons />

                        </div>
                    </form>

                    @else
                    <div class="text-center py-5">
                        <i class="bi bi-cart-x fs-1 text-muted mb-3 d-block"></i>
                        <p class="text-muted mb-0">Empty. Add a product Ordered to get started.</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>