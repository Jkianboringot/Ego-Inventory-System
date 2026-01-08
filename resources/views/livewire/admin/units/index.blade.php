<div wire:init="loadData">
    <x-slot:header>Units</x-slot:header>

    <div class="card shadow">
        <x-livewire.partials.new-header-form title="Units" route="units" />

        <div class="card-body">

            <x-livewire.partials.search-bar :$search />

            @if($readyToLoad)
            <x-livewire.partials.empty-result-form :var="$units" msg="Unit" :search="$search">

                <div class="table-responsive">

                    <table class="table table-hover align-middle ">
                        <thead class="table-light">
                            <tr class="text-center ">

                                <th>Name</th>
                                <th>Symbol</th>
                                <th>Number of Products</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($units as $unit)
                            <tr class="text-center">

                                <td>{{ $unit->name }}</td>
                                <td>{{ $unit->symbol??'N/A' }}</td>
                                <td><strong>{{ $unit->products_count }}</strong></td>


                                <td class="text-center">
                                    <x-livewire.partials.edit-delete-form route="units" id="{{$unit->id}}" />
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div class="mt-3">
                        {{ $units->links() }}

                    </div>
            </x-livewire.partials.empty-result-form>
            @else
            <x-livewire.partials.loading-modal-form />


            @endif
        </div>
    </div>
</div>
</div>