@props(['header','organization_types'])


<div>
    <x-livewire.partials.cancel-buttons />

    <x-slot:header>Customers</x-slot:header>
    <div class="card" tabindex="0" wire:keydown.escape="cancel">

        <div class="card-header bg-inv-primary text-inv-secondary border-0">
            <h5>{{ $header }}</h5>
        </div>
        <form
            wire:submit.prevent="save">
            <div class="card-body">

                <div class="row">
                    <div class="col-md-6 col-12">
                        <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input wire:model.defer='customer.name' type="text" class="form-control" name="name"
                                id="name" aria-describedby="name" placeholder="Enter customer's name" maxlength="75" minlength="2" required/>
                            @error('customer.name')
                            <small id="" class="form-text text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6 col-12">
                        <div class="mb-3">
                            <label for="tax_id" class="form-label">TIN ID</label>
                            <input wire:model.defer='customer.tax_id' type="text" class="form-control" name="tax_id"
                                id="tax_id" aria-describedby="" placeholder="Enter Tax ID" maxlength="20" minlength="2" required/>
                            @error('customer.tax_id')
                            <small id="" class="form-text text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6 col-12">
                        <div class="mb-3">
                            <label for="phone_number" class="form-label">Phone Number</label>
                            <input wire:model.defer='customer.phone_number' type="text" class="form-control" name="phone_number"
                                id="phone_number" aria-describedby="phone_number" placeholder="Enter phone number (optional)" maxlength="15" minlength="2"/>
                            @error('customer.phone_number')
                            <small id="" class="form-text text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6 col-12">
                        <div class="mb-3">
                            <label for="organization_type" class="form-label">Organization Type</label>
                            <select wire:model.defer='customer.organization_type' class="form-select">
                                <option value="null" selected>Select the Organization Type</option>
                                @foreach ($organization_types as $organization_type )
                                <option value="{{$organization_type}}">{{ $organization_type }}</option>
                                @endforeach
                            </select>


                            @error('customer.organization_type')
                            <small id="" class="form-text text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>




                    <div class="mb-3">
                        <label for="address" class="form-label">Physical Address</label>
                        <textarea wire:model.defer='customer.address' class="form-control" name="address" id="address" rows="3" minlength='2' maxlength="255"
                        placeholder="Add customer address (optional)"></textarea>
                        @error('customer.address')
                        <small id="" class="form-text text-danger">{{ $message }}</small>
                        @enderror
                    </div>



                </div>
                <form wire:submit.prevent="save">

                    <div class="d-flex justify-content-end gap-2 mt-3">
                        <x-livewire.partials.cancelProcess-button />
                        <x-livewire.partials.save-buttons />

                    </div>
                </form>
            </div>
        </form>
    </div>
</div>