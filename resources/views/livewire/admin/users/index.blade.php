<div wire:init="loadData">
    <x-slot name="head">Users</x-slot>
    <div class="card shadow">

        <x-livewire.partials.new-header-form title="Users" route="users" />
        <div class="card-body table-responsive">
            @if($readyToLoad)


            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        @if($op)

                        <th>#####</th>
                        @endif
                        <th>Role</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)

                    <tr>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        @if($op)
                        <td>

                            @foreach($user->plainText as $text)
                            <x-livewire.partials.remarks-reason-modal :var="$text->plain" />

                            @endforeach
                        </td>
                        @endif
                        <td>
                            @foreach($user->roles as $role)
                            <li>{{ $role->title }}</li>
                            @endforeach
                        </td>

                        <td class="text-center">
                            <div class="d-flex flex-wrap gap-1 justify-content-center">
                                <x-livewire.partials.edit-delete-form route="users" id="{{ $user->id }}" />
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="mt-3">
                {{ $users->links() }}
            </div>

            @else
            <x-livewire.partials.loading-modal-form />

            @endif
        </div>
    </div>
</div>