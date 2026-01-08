<div wire:init="loadData">
    <x-slot name="header">Roles</x-slot>

    <div class="card shadow">

        <div class="card-header bg-inv-primary text-inv-secondary border-0 d-flex justify-content-between align-items-center">
            <h5>Role List</h5>
        </div>

        <div class="card-body table-responsive">
            @if($readyToLoad)
           
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                    <tr>
                        <th>Title</th>
                        <th class="text-center">Permissions</th>
                        <!-- <th class="text-center">Actions</th> -->
                    </tr>
                </thead>

                <tbody>
                    @foreach ($roles as $role)
                    <tr>
                        <td>{{ $role->title }}</td>
                        <td class="text-center">
                            @foreach($role->permissions ?? [] as $permission)
                            <span class="badge bg-primary me-1">{{ $permission }}</span>
                            @endforeach
                        </td>
                        {{-- <td class="text-center">
                            <div class="d-flex flex-wrap gap-1 justify-content-center">
                                <button wire:confirm="Are you sure you want to delete this record?"
                                    class="btn btn-danger"
                                    wire:click='delete({{ $role->id }})'>
                                    <i class="bi bi-trash-fill"></i>
                                </button>
                            </div>
                        </td> --}}
                    </tr>
                    @endforeach
                </tbody>
            </table>

            {{-- Pagination --}}
            <div class="mt-3 px-3">
                {{ $roles->links() }}
            </div>

            @else
            <x-livewire.partials.loading-modal-form />

            @endif
        </div>

    </div>
</div>