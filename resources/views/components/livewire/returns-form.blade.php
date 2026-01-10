@props(['productSearch','products','productList','return','selectedProductId','loadedProducts'=>'','type'=>false])

<div>
    <x-livewire.partials.cancel-buttons />
    <x-slot:header>Returns</x-slot:header>

    <div class="row justify-content-center">

        {{-- LEFT SIDE --}}
        <div class="col-md-4 col-6">

            {{-- CARD 1 : Return Details --}}
            <div class="card mb-2">
                <div class="card-header bg-inv-primary text-inv-secondary border-0">
                    <h6 class="text-center">Return Details</h6>
                </div>

                <div class="card-body">

                    <div class="mb-3">
                        <label class="form-label">Return Type</label>
                        <select wire:model.defer="return.return_type" class="form-control" required>
                            <option value="">Select return type</option>
                            <option value="refunded">Refunded</option>
                            <option value="exchanged">Exchanged</option>
                        </select>
                        @error('return.return_type')
                        <small class="form-text text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Sale Invoice</label>
                        <input wire:model.defer="return.sale_invoice" type="text"
                               maxlength="50" minlength="1"
                               class="form-control"
                               placeholder="Enter Sale Invoice" />
                        @error('return.sale_invoice')
                        <small class="form-text text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Reason</label>
                        <textarea wire:model.defer="return.reason"
                                  class="form-control"
                                  maxlength="255"
                                  minlength="3"
                                  required
                                  placeholder="Add reason for return (required)"></textarea>
                        @error('return.reason')
                        <small class="form-text text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                </div>
            </div>

            {{-- CARD 2 : Add Returned Product --}}
            <form wire:submit.prevent="addToList">
                <div class="card">
                    <div class="card-header bg-inv-primary text-inv-secondary border-0">
                        <h6 class="text-center">Add Returned Product</h6>
                    </div>

                    <div class="card-body">

                        <x-livewire.partials.product-search
                            :$products
                            :$selectedProductId
                            :$productSearch
                        />

                        <div class="row">

                            <div class="col-6">
                                <div class="mb-3">
                                    <label class="form-label">Quantity</label>
                                    <input wire:model.defer="quantity"
                                           type="number"
                                           min="0.01"
                                           step="0.01"
                                           max="999999.99"
                                           class="form-control"
                                           placeholder="Enter quantity"
                                           required />
                                    @error('quantity')
                                    <small class="form-text text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-6">
                                <div class="mb-3">
                                    <label class="form-label">Adds On</label>
                                    <input wire:model.defer="adds_on"
                                           type="number"
                                           step="0.1"
                                           min="1"
                                           max="999999.99"
                                           class="form-control"
                                           placeholder="Adds On (Optional)">
                                    @error('adds_on')
                                    <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                        </div>

                        <x-livewire.partials.add-to-list-buttons />

                    </div>
                </div>
            </form>

        </div>

        {{-- RIGHT SIDE (UNCHANGED) --}}
        <div class="col-md-8 col-6">
            <div class="card shadow" id="cart-section">
                <div class="card-header text-inv-primary border-0">
                    <h5 class="text-center text-uppercase">Returned Products</h5>
                </div>

                <div class="card-body">
                    @if ($productList && count($productList) > 0)
                        <table class="table" style="table-layout: fixed;">
                            <thead>
                                <tr>
                                    <th>Product Name</th>
                                    <th class="text-center">Quantity</th>
                                    <th>Refunded Amount</th>
                                    <th>Adds On Amount</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($productList as $key => $listItem)
                                <tr>
                                    <td>
                                        @php
                                            if($type){
                                                $product = collect($loadedProducts)->firstWhere('id', $listItem['product_id']);
                                            } else {
                                                $product = $return->products->firstWhere('id', $listItem['product_id']);
                                            }

                                            if (!$product) {
                                                $product = \App\Models\Product::select(['name'])->find($listItem['product_id']);
                                            }
                                        @endphp

                                        {{ $product?->name ?? 'Unknown Product' }}
                                    </td>

                                    <td>
                                        <x-livewire.partials.quantity-input :$key />
                                    </td>

                                    <td>{{ number_format($listItem['price'], 2) ?? 0 }}</td>
                                    <td>{{ number_format($listItem['adds_on'], 2) }}</td>

                                    <td class="text-center">
                                        <x-livewire.partials.add-sub-delete-form
                                            :listItem="$listItem"
                                            :key="$key"
                                        />
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
                            <p class="text-muted mb-0">Empty. Add product to return get started.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

    </div>
</div>
