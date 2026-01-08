@props(['productSearch','products','productList','addProduct','selectedProductId','loadedProducts'=>'','type'=>false])

<div tabindex="0" wire:keydown.escape="cancel">

    <x-livewire.partials.cancel-buttons />

    <x-slot:header>Add Product</x-slot:header>

    <div class="row g-3">
        <div class="col-lg-4 col-md-5">
            <form wire:submit.prevent="addToList">

                <div class="card">
                    <div class="card-header bg-inv-primary text-inv-secondary border-0">
                        <h6 class="text-center">Add Arrive Product</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <x-livewire.partials.product-search :$products :$selectedProductId :$productSearch />
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Quantity</label>
                            <input wire:model.defer="quantity" type="number" min="0.01" step="0.01"
                             max="999999.99" class="form-control" 
                            placeholder="Enter quantity" required />
                            @error('quantity')
                            <small class="form-text text-danger">{{ $message }} </small>
                            @enderror
                        </div>

                        <x-livewire.partials.add-to-list-buttons />
                    </div>
                </div>
            </form>
        </div>

        {{-- RIGHT SIDE (Cart) --}}
        <div class="col-lg-8 col-md-7">
            <div class="card">
                <div class="card-header">
                    <h5 class="text-center text-uppercase mb-0 fw-semibold text-white">Arrive Product</h5>
                </div>

                <div class="card-body">
                    @if ($productList && count($productList) > 0)
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>Product Name</th>
                                    <th class="text-center">Quantity</th>
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
                                        }
                                        else{
                                        $product = $addProduct->products->firstWhere('id', $listItem['product_id']);
                                        }

                                        if (!$product) {
                                        $product = \App\Models\Product::select(['name'])->find($listItem['product_id']);
                                        }
                                        @endphp

                                        @if($product)
                                        <span class="fw-medium">{{ $product?->name ?? 'Unknown Product' }}</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <x-livewire.partials.quantity-input :$key />
                                    </td>
                                    <td class="text-center">
                                        <x-livewire.partials.add-sub-delete-form :listItem="$listItem" :key="$key" />
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <form wire:submit.prevent="save">
                        <div class="d-flex justify-content-end gap-2 mt-3 pt-3 border-top">
                            <x-livewire.partials.cancelProcess-button />
                            <x-livewire.partials.save-buttons />
                        </div>
                    </form>

                    @else
                    <div class="text-center py-5">
                        <i class="bi bi-cart-x fs-1 text-muted mb-3 d-block"></i>
                        <p class="text-muted mb-0">Empty. Add a newly arrive products to get started.</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>