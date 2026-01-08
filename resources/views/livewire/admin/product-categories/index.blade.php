<div wire:init="loadData">
    <x-slot:head>Product Categories</x-slot:head>


    <div class="card shadow">
        <x-livewire.partials.new-header-form title="Product Categories" route="productcategories" />
        <div class="card-body">
            <x-livewire.partials.search-bar :$search />

            @if($readyToLoad)
            <x-livewire.partials.empty-result-form :var="$productCategories" msg="Product Category" :search="$search">

                <div class="table-responsive">

                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr class="text-center">

                                <th>Name</th>
                                <th>Number of Products</th>

                                <th>Actions</th>

                            </tr>
                        </thead>
                        <tbody>
                            @foreach($productCategories as $category)
                            <tr class="text-center">

                                <td> {{ $category->name }}</td>
                                <td><strong>{{ $category->products_count }}</strong></td>
                                <td>
                                    <x-livewire.partials.edit-delete-form route="productcategories" id="{{$category->id}}" />
                                </td>

                            </tr>
                            @endforeach

                        </tbody>
                    </table>

                    <div class="mt-3">
                        {{ $productCategories->links() }}

                    </div>
                   </x-livewire.partials.empty-result-form>
                    @else
                    <x-livewire.partials.loading-modal-form />


                    @endif
                </div>
        </div>

    </div>
</div>