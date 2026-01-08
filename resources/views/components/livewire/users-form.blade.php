@props(['header','roles'])
<div>
    <x-slot:head>Users</x-slot:head>

    <x-livewire.partials.cancel-buttons />

    <div class="card" tabindex="0" wire:keydown.escape="cancel">

        <div class="card-header bg-inv-primary text-inv-secondary border-0">
            <h5>{{ $header }}</h5>
        </div>
        <form wire:submit.prevent="save">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input wire:model.defer='user.name' type="text" class="form-control" name="name" id="name"

                                placeholder="Enter your username (e.g Joe doe)" required maxlength="75" minlength="5" />
                            @error('user.name')
                            <small class="form-text text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input wire:model.defer='user.email' type="email" class="form-control" name="email" id="email"
                                placeholder="Enter your email (e.g email@example.com)" minlength="10" maxlength="150" required/>
                            @error('user.email')
                            <small class="form-text text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6 col-12">
                        <div class="form-group mb-3">
                            <label for="Roles" class="form-label">Roles</label>
                            <select wire:model.defer="selectedRoles"
                                multiple
                                class="form-select form-select-lg"
                                required>
                                @foreach ($roles as $role)
                                <option value="{{ $role->id }}">{{ $role->title }}</option>
                                @endforeach
                            </select>
                             @error('selectedRoles')
                            <small class="form-text text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="plain" class="form-label">Password</label>
                            <input wire:model.defer='plain' type="password" class="form-control" name="plain" id="plain"
                                placeholder="Enter your User's Password" maxlength="50" minlength="3"/>
                            @error('plain')
                            <small class="form-text text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 mt-3">
                    <x-livewire.partials.cancelProcess-button />

                    <x-livewire.partials.save-buttons />
                </div>
            </div>
        </form>
    </div>
</div>