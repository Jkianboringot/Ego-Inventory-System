<div wire:init="loadData">
    <x-slot:head>Brands</x-slot:head>

    <div class="card shadow">

        <x-livewire.partials.new-header-form title="Brands" route="brands" />

        <div class="card-body">
            <x-livewire.partials.search-bar :$search />

            @if($readyToLoad)
            <x-livewire.partials.empty-result-form :var="$brands" msg="Brand" :search="$search">

                <div class=" table-responsive">

                    <table class="table table-hover align-middle">
                        <thead class="table-light">

                            <tr class="text-center">

                                <th>Name</th>
                                <th>Number of Products</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($brands as $brand)
                            <tr class="text-center">

                                <td>{{ $brand->name }}</td>

                                <td><strong>{{ $brand->products_count }}</strong></td>

                                <td>


                                    <x-livewire.partials.edit-delete-form route="brands" id="{{$brand->id}}" />


                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div class="mt-3">
                        {{ $brands->links() }}

                    </div>
               </x-livewire.partials.empty-result-form>
                    @else
                    <x-livewire.partials.loading-modal-form />

                    @endif


                </div>
        </div>
    </div>
</div>