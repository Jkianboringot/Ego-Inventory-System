@props(['supplierSearch',
'paidOptions',
'suppliers',
'productSearch',
'products',
'purchase',
'productList',
'selectedProductId','loadedProducts'=>'','type'=>false])




<div>
    <x-livewire.partials.cancel-buttons />

    <x-slot:header>Purchases</x-slot:header>
    <div class="row justify-content-center">
        {{-- LEFT SIDE --}}
        <div class="col-md-4 col-6">

            <div class="card">
                <div class="card-header bg-inv-primary text-inv-secondary border-0">
                    <h6 class="text-center">Set Status & Supplier</h6>
                </div>

                <div class="card-body">
                    <div class="row">


                        <div class="mb-3">
                            <label for="" class="form-label">Paid Status</label>

                            <select wire:model.defer="purchase.is_paid"
                                class="form-select" name="" id="" required>
                                <option value="null" selected>Select Status</option>
                                @foreach ($paidOptions as $option)

                                <option class={{ in_array($option,['Paid','Partially_Paid']) ? 'text-success' : 'text-danger'  }}
                                    value="{{ $option }}">{{ $option }}</option>
                                <!-- just create an array fro this -->
                                @endforeach
                            </select>

                            @error('purchase.is_paid')
                            <small id="helpId" class="form-text text-danger">{{ $message }} </small>
                            @enderror
                        </div>


                        <div class="col-md-6">
                            @if( $purchase && in_array($purchase->is_paid, ['Paid', 'Partially Paid']) )

                            <div class="mb-3">
                                <label for="" class="form-label">Date Settled</label>
                                <input wire:model.defer="purchase.date_settled" type="date" class="form-control" />
                                @error('purchase.date_settled')
                                <small id="helpId" class="form-text text-danger">{{ $message }} </small>
                                @enderror
                            </div>
                            @endif
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="" class="form-label">Supplier Search</label>
                        <input type="text" wire:model.live='supplierSearch' class="form-control" required placeholder="Type supplier's name (optional)" />
                        @error('purchase.supplier_id')
                        <small id="helpId" class="form-text text-danger">{{ $message }}</small>
                        @enderror
                        <ul class="list-group mt-2 w-100">
                            @if ($supplierSearch != '')
                            @foreach ($suppliers as $supplier)
                            <x-supplier-list-item :supplier="$supplier" :purchase="$purchase" />
                            @endforeach
                            @endif
                        </ul>
                    </div>
                </div>

            </div>
            <form wire:submit.prevent="addToList">

                <div class="card mt-2">
                    <div class="card-header bg-inv-primary text-inv-secondary border-0">
                        <h6 class="text-center">Add Product Purchase</h6>
                    </div>
                    <div class="card-body">

                        <x-livewire.partials.product-search :$products :$selectedProductId :$productSearch />


                        <div class="row">
                            <div class="col-md-6">

                                <div class="mb-3">
                                    <label for="" class="form-label">Quantity</label>
                                    <input wire:model.defer='quantity' type="number" min="0.01" step="0.01" max="999999.99" class="form-control"
                                        placeholder="Enter quantity" required />
                                    @error('quantity')
                                    <small id="helpId" class="form-text text-danger">{{ $message }} </small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="" class="form-label">Purchase Price</label>
                                    <input wire:model.defer='price' type="number" class="form-control"
                                        readonly placeholder="Auto inputed" required />
                                    @error('price')
                                    <small id="helpId" class="form-text text-danger">{{ $message }} </small>
                                    @enderror
                                </div>
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
                    <h5 class="text-center text-uppercase">Purchase Products</h5>
                </div>

                <div class="card-body">
                    @if ($productList && count($productList) > 0)
                    <table class="table" style="table-layout: fixed;">
                        <thead>
                            <tr>



                                <th>Product Name</th>
                                <th>Product Quantity</th>
                                <th>Unit Price</th>
                                <th>Total Amount</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $total = 0; @endphp
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
                                    @endphp

                                    @if($product)
                                    {{ $product?->name ?? 'Unknown Product' }}


                                    @endif
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
                            @php $total += $listItem['quantity'] * $listItem['price']; @endphp
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
                        <p class="text-center text-muted">Empty. Add a product to purchase to get started.</p>

                    </div>

                    @endif
                </div>

            </div>

        </div>
    </div>
</div>