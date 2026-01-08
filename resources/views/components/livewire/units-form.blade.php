@props(['header'])

<div>
    <x-slot:header>Units</x-slot:header>
    <x-livewire.partials.cancel-buttons />

    <div class="card" tabindex="0" wire:keydown.escape="cancel">
        <div class="card-header bg-inv-primary text-inv-secondary border-0">
            <h5>{{ $header }}</h5>
        </div>
        <form wire:submit.prevent="save">
            <div class="card-body">

                <div class="row">
                    <div class="col-md-6 col-12">
                        <div class="mb-3">
                            <label for="name" class="form-label">Unit Name</label>
                            <input wire:model.defer='unit.name' type="text" class="form-control" name="name"
                                id="name" aria-describedby="" placeholder="Enter unit name (e.g kilogram)"  maxlength="50" minlength="5" required />
                            @error('unit.name')
                            <small id="" class="form-text text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6 col-12">
                        <div class="mb-3">
                            <label for="symbol" class="form-label">Unit Symbol</label>
                            <input wire:model.defer='unit.symbol' type="text" class="form-control" name="symbol"
                                id="symbol" aria-describedby="" placeholder="Enter unit symbol (e.g kg)" maxlength="5" minlength="2" required/>
                            @error('unit.symbol')
                            <small id="" class="form-text text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 mt-3">
                    <x-livewire.partials.cancelProcess-button />
                    <x-livewire.partials.save-buttons />

                </div>
        </form>
    </div>
</div>