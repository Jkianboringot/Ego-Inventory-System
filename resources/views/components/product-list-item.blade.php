@props(['product', 'selectedProductId'])

<li wire:click='selectProduct({{ $product->id }})'
    wire:key="product-item-{{ $product->id }}"
    class="list-group-item d-flex align-items-center px-4 py-3 gap-4
           {{ $product->id == $selectedProductId ? 'active' : '' }}">

    <div style="max-width: 30%">
        <h6 class="mb-1">{{ $product->name }}</h6>
        <small class="text-muted">{{ $product->location??"N/A" }}</small>

        <div>
            <x-livewire.partials.remarks-reason-modal :var="$product->description" />

        </div>


    </div>

    <div class="{{ $product->inventory_balance > 0 ? 'text-cash-green' : 'text-cash-red' }}">
        <small class="text-muted d-block">Available Units:</small>
        <h5 class="mb-0">
            {{ rtrim(rtrim(number_format($product->inventory_balance ?? 0, 2), '0'), '.') }}

        </h5>
    </div>

    <div class="ms-auto text-end">
        <small class="text-muted d-block">Selling Price</small>
        <h6 class="mb-0">
            {{ number_format($product->sale_price) }}
        </h6>
    </div>
</li>