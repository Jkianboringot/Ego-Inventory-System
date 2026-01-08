@props(['productSearch','products','productList','defected','selectedProductId','loadedProducts'=>'','type'=>false,'productCache' => []])

<div tabindex="0" wire:keydown.escape="cancel">
    <x-livewire.partials.cancel-buttons />

    <x-slot:header>Defected Products</x-slot:header>

    @if (session()->has('warning'))
    <div class="alert alert-warning alert-dismissible  d-flex justify-content-between align-items-center fade show" role="alert">

        <span>{{ session('warning') }}</span>
        <button wire:click="continueAnyway" class="btn btn-warning  btn-outline-dark ms-2">
            <strong>Continue Anyway</strong>
        </button>
    </div>
    @endif
    <div class="row justify-content-center">
        <div class="col-md-4 col-6">
            <form wire:submit.prevent="addToList">


                <div class="card shadow">
                    <div class="card-header bg-inv-primary text-inv-secondary border-0">
                        <h5 class="text-center ">Add Defected Product</h5>
                    </div>
                    <div class="card-body">

                        <x-livewire.partials.product-search :$products :$selectedProductId :$productSearch />


                        <div class="mb-3">
                            <label class="form-label">Quantity</label>
                            <input wire:model.defer="quantity" type="number" min="0.01" step="0.01" class="form-control"
                                placeholder="Enter quantity" required />
                            @error('quantity')
                            <small class="form-text text-danger">{{ $message }} </small>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Reason</label>
                            <textarea wire:model.defer="defected.remarks" class="form-control" maxlength="255" minlength="20"
                                placeholder="Add reason (optional)"></textarea>
                            @error('defected.remarks')
                            <small class="form-text text-danger">{{ $message }}</small>
                            @enderror
                        </div>



                        {{-- keep the partial, but ensure its buttons are type="button" (see partial edit below) --}}
                        <x-livewire.partials.add-to-list-buttons />
                    </div>
                </div>
            </form> {{-- end left container --}}
        </div>

        {{-- RIGHT SIDE (Cart) --}}
        <div class="col-md-8 col-6">
            <div class="card shadow" id="cart-section">
                <div class="card-header  text-inv-primary border-0">
                    <h5 class="text-center text-uppercase">Defected Products</h5>
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
                                    if($type){
                                    $product = collect($loadedProducts)->firstWhere('id', $listItem['product_id']);
                                    }
                                    else{
                                    $product = $defected->products->firstWhere('id', $listItem['product_id']);
                                    }

                                    if (!$product) {
                                    $product = \App\Models\Product::select(['name'])->find($listItem['product_id']);
                                    }
                                    @endphp

                                    @if($product)
                                    {{ $product?->name ?? 'Unknown Product' }}
                                    <!-- temporary fix -->

                                    @endif
                                </td>

                                <td>
                                    <x-livewire.partials.quantity-input :$key />
                                </td>
                                <td>{{ number_format($listItem['price'], 2) }}</td>

                                <td>{{ number_format($listItem['quantity'] * $listItem['price'], 2) }}</td>



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
                        <p class="text-muted mb-0">Empty. Add a product to damage product get started.</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>