@props(['productSearch','products','productList','unsuccessfulTransaction','selectedProductId','loadedProducts'=>'','type'=>false])

<div tabindex="0" wire:keydown.escape="cancel">
    <x-livewire.partials.cancel-buttons />

    <div class="row justify-content-center">
        <div class="col-md-4 col-6">
            <div wire:keydown.ctrl.enter.prevent="addToList">


                <div class="card ">
                    <div class="card-header bg-inv-primary text-inv-secondary border-0">
                        <h6>Add Unsuccessfull Transactions to List</h6>
                    </div>
                    <div class="card-body">


                        <x-livewire.partials.product-search :$products :$selectedProductId :$productSearch />


                        <div class="mb-3">
                            <label class="form-label">Quantity</label>
                            <input wire:model.defer="quantity" type="number" min="0.01" step="0.01" class="form-control" placeholder="Enter quantity" required />
                            @error('quantity')
                            <small class="form-text text-danger">{{ $message }} </small>
                            @enderror
                        </div>




                        {{-- keep the partial, but ensure its buttons are type="button" (see partial edit below) --}}
                        <x-livewire.partials.add-to-list-buttons />
                    </div>
                </div>
            </div> {{-- end left container --}}
        </div>

        {{-- RIGHT SIDE (Cart) --}}
        <div class="col-md-8 col-6">
            <div class="card shadow">
                <div class="card-header  text-inv-primary border-0">
                    <h5 class="text-center text-uppercase">Unsuccessfull Transactions List</h5>
                </div>

                <div class="card-body">
                    {{-- use wire:submit.prevent here so Enter will submit this form only --}}
                    @if ($productList && count($productList) > 0)
                    <table class="table" style="table-layout: fixed;">
                        <thead>
                            <tr>
                                <th>Product Name</th>
                                <th class="text-center">Product Quantity</th>
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
                                    $product = $unsuccessfulTransaction->products->firstWhere('id', $listItem['product_id']);
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
                                    </strong>
                                </td>
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
                    <p class="text-center text-muted">Your cart is empty. Add a product to get started.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>