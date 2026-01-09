@props(['header'])


<div>
    <x-livewire.partials.cancel-buttons />

    <x-slot:header>Product Categories</x-slot:header>

    <div class="card" tabindex="0" wire:keydown.escape="cancel">
        <div class="card-header bg-inv-primary text-inv-secondary border-0">
            <h5>{{ $header }}</h5>
        </div>
        <form wire:submit.prevent="save">

            <div class="card-body">

                <div class="mb-3">
                    <label for="name" class="form-label">Category Name</label>
                    <input wire:model.defer='category.name' type="text" class="form-control" name="name" id="name"
                        aria-describedby="" placeholder="Enter category's name (e.g fishing supplies)" maxlength="75" minlength="1" />
                    @error('category.name')
                    <small id="" class="form-text text-danger">{{ $message }}</small>
                    @enderror
                </div>
                <div class="d-flex justify-content-end gap-2 mt-3">
                    <x-livewire.partials.cancelProcess-button />
                    <x-livewire.partials.save-buttons />

                </div>

            </div>
        </form>
    </div>
</div>