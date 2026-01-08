<!-- @props(['header','staticpermissions'])
<div>
    <x-slot:header>Roles</x-slot:header>
    <x-livewire.partials.cancel-buttons />

    <div class="card" tabindex="0" wire:keydown.escape="cancel">
        <div class="card-header bg-inv-primary text-inv-secondary border-0">
            <h5>{{$header}}</h5>
        </div>
        <div class="card-body">
            <form
                wire:submit.prevent="save">
                <div class="row">
                    <div class="col-md-6 col-12">
                        <div class="mb-3">
                            <label for="title" class="form-label">Role Title</label>
                            <input wire:model.defer='role.title' type="text" class="form-control" name="title"
                                id="title" aria-describedby="" placeholder="Enter your Role Title" />
                            @error('role.title')
                            <small id="" class="form-text text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                    </div>

                    <div class="col-md-6 col-12">
                        <div class="mb-3">
                            <label for="staticpermissions" class="form-label">Permission</label>
                            <select wire:model.defer='selected_permissions' class="form-select" multiple>
                                <option disabled>Select the Role Permission</option>

                                @foreach ($staticpermissions as $permission)
                                <option value="{{ $permission }}">{{ $permission }}</option>
                                @endforeach
                            </select>


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
</div> -->