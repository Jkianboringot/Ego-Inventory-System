@props(['listItem','key'])
{{-- @if ($listItem['quantity'] > 1)
 <button wire:click='subtractQuantity({{ $key }})' class="btn btn-danger">
     <i class="bi bi-dash"></i>
 </button>
 @endif
 <button wire:click='addQuantity({{ $key }})' class="btn btn-success">
     <i class="bi bi-plus"></i>
 </button> --}}
 <button onclick="confirm('Are you sure you want to delete this?')||event.stopImmediatePropagation()"
     wire:click='deleteCartItem({{ $key }})'
     class="btn btn-danger">
     <i class="bi bi-x-lg"></i>
 </button>