@props(['header'])


<div>
    <x-slot:header>Suppliers</x-slot:header>
    <x-livewire.partials.cancel-buttons />

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
                            <input wire:model.defer='supplier.name' type="text" class="form-control" name="name"
                                id="name" aria-describedby="name" placeholder="Enter supplier's name" maxlength="75" minlength="2" required/>
                            @error('supplier.name')
                            <small id="" class="form-text text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6 col-12">
                        <div class="mb-3">
                            <label for="account_number" class="form-label">Account Number</label>
                            <input wire:model.defer='supplier.account_number' type="text" class="form-control" name="account_number"
                                id="account_number" aria-describedby="" placeholder="Enter supplier's account number (optional)" maxlength="35" minlength="2" />
                            @error('supplier.account_number')
                            <small id="" class="form-text text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6 col-12">
                        <div class="mb-3">
                            <label for="phone_number" class="form-label">Phone Number</label>
                            <input wire:model.defer='supplier.phone_number' type="text" class="form-control" name="phone_number"
                                id="phone_number" aria-describedby="phone_number" placeholder="Enter phone number (optional)" maxlength="15"/>
                            @error('supplier.phone_number')
                            <small id="" class="form-text text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>


                    <div class="col-md-6 col-12">
                        <div class="mb-3">
                            <label for="tax_id" class="form-label">TIN ID</label>
                            <input wire:model.defer='supplier.tax_id' type="text" class="form-control" name="tax_id"
                                id="tax_id" aria-describedby="" placeholder="Enter Tax ID" maxlength="20" minlength="1" required />
                            @error('supplier.tax_id')
                            <small id="" class="form-text text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="address" class="form-label">Physical Address</label>
                        <textarea wire:model.defer='supplier.address' class="form-control" name="address" id="address" rows="3" minlength="2" maxlength="255"
                        placeholder="Add supplier address (optional)"></textarea>
                        @error('supplier.address')
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