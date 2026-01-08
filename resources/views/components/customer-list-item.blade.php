@props(["customer", 'category'])

<li wire:click="selectCustomer({{ $customer->id }})"
    class="list-group-item {{ $customer->id == $category->customer_id ? 'active' : '' }} d-flex justify-content-between">
    <div class="me-auto">
        <h7 class='text-inv-secondary'>{{ $customer->name }}</h7><br>
        <small class='text-muted'>{{ $customer->tax_id}} <br>{{ $customer->phone_number }}</small>
    </div>
  
    <!-- <div class="ms-auto my-auto {{$customer->total_balance > 0 ? 'text-cash-green' : 'text-cash-red' }} ">
        <h7>PISO {{ number_format($customer->total_balance, 2) }}</h7>

    </div> -->
</li>