@props(['header','brand'])


<div>
    <x-livewire.partials.cancel-buttons />

    <x-slot:header>Brands</x-slot:header>

    <div class="card" tabindex="0" wire:keydown.escape="cancel">
        <div class="card-header bg-inv-primary text-inv-secondary border-0">
            <h5>{{ $header }}</h5>
        </div>

        <form wire:submit.prevent="save">
            <div class="card-body">

                <div class="row g-3">

                    {{-- Brand Name --}}
                    <div class="col-12">
                        <label for="name" class="form-label">Name</label>
                        <input wire:model.defer='brand.name' type="text" class="form-control" name="name"
                            id="name" placeholder="Enter brand's name (e.g Nike)" maxlength="75" minlength="1" required/>
                        @error('brand.name')
                        <small class="form-text text-danger">{{ $message }}</small>
                        @enderror
                    </div>




                </div>

                {{-- Action buttons --}}
                <div class="mt-4 d-flex justify-content-end gap-2">
                    <x-livewire.partials.cancelProcess-button />
                    <x-livewire.partials.save-buttons />

                </div>

            </div>
        </form>
    </div>
</div>