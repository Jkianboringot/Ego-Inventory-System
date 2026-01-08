@props(["supplier", 'purchase'])
 <!-- // this was purchase i dont know if other function are using this but if error realted to this just change addProducts to purchases -->

<li wire:click="selectSupplier({{ $supplier->id }})"
    class="list-group-item {{ $supplier->id == $purchase->supplier_id ? 'active' : '' }} d-flex justify-content-between">
    <div class="me-auto ">
        <h7 class='text-inv-secondary'>{{ $supplier->name }}</h7><br>
        <small class='text-muted'> {{ $supplier->tax_id }}</small>
    </div>
<!--   
    <div class="ms-auto my-auto {{$supplier->total_balance > 0 ? 'text-cash-green' : 'text-cash-red' }} ">
        <h7>PISO {{ number_format($supplier->total_balance, 2) }}</h7>

    </div> -->
</li>   