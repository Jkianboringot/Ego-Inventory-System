@props(['key'])
<input
    type="number"
    min="1" 
    step="1"
    wire:model="productList.{{ $key }}.quantity"
    class="form-control form-control-sm text-center"
    style="width: 80px; margin: auto;"
    required
     />

@error("productList.$key.quantity")
<small class="text-danger">{{ $message }}</small>
@enderror

    <!-- for testing only, after make it min 1 -->
