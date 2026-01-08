@props(['products','selectedProductId','productSearch'])
<div class="mb-3" x-data x-init="$refs.searchInput.focus()" x-on:refocus-search.window="$refs.searchInput.focus()">
    <label class="form-label">Product Search</label>
    <input
        type="text"
        wire:model.live="productSearch"
        placeholder="Enter product name or barcode"
        class="form-control"
        x-ref="searchInput" required />
    @error('selectedProductId')
    <small class="form-text text-danger">{{ $message }} </small>
    @enderror
    <!-- 
        i can actaully make  wire:model.live lazy if need becuase , barocde scanner actaully
        have an enter input along with the barcode '23234(enter)' so lazy will work fine
        with it, the problem is when you type its not dynamic becuase you have to click
        enter , so ask them about this -->
    <ul class="list-group mt-2 w-100">
        @if ($productSearch != '')
        @forelse ($products as $product)
        <x-product-list-item
            :product="$product"
            :selectedProductId="$selectedProductId"
            wire:key="product-list-item-{{ $product->id }}" />
        @empty
        <p>No Products yet.</p>
        @endforelse
        @endif
    </ul>
</div>