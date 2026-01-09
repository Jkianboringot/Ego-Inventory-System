<div wire:init="loadData" x-data="{ descriptionModal: '', supplierModal: [] }">
    <x-slot:header>Products</x-slot:header>


    <div class="card shadow ">
        <x-livewire.partials.new-header-form title="Products" route="products" />

        <div class="card-body">
            <x-livewire.partials.search-bar :$search />

            @if($readyToLoad)
            <x-livewire.partials.empty-result-form :var="$products" msg="Product" :search="$search">


                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr class="text-center">
                                <th>Barcode</th>
                                <th>Location</th>
                                <th>Product</th>
                                @role(['Admin','Supervisor'])
                                <th>Supplier</th>
                                @endrole
                                <th>Brand</th>
                                <th>Category</th>
                                <th>Stock</th>

                                <th>Selling Price</th>
                                @role(['Admin','Supervisor'])

                                <th>Unit Cost</th>
                                @endrole

                                @role(['Admin','Supervisor','Inventory Clerk'])
                                <th>Actions</th>
                                @endrole

                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($products as $product)
                            <tr class="text-center">
                                <td>
                                    <span class="md-1"><strong>{{ $product->barcode }}</strong></span>
                                </td>
                                <td>
                                    @if($product->location)
                                    <i class="bi bi-geo-alt-fill text-primary me-1"></i>
                                    <small>{{ $product->location }}</small>
                                    @else
                                    <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                                <td>
                                    <div>
                                        <strong class="mb-1">{{ $product->name }}</strong>
                                        <x-livewire.partials.remarks-reason-modal :var="$product->description" />

                                    </div>
                                </td>
                                @role(['Admin','Supervisor'])
                                <td>
                                    @if($product->supplier)
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-truck text-info me-2"></i>
                                        <span>{{ $product->supplier->name }}</span>
                                    </div>
                                    @else
                                    <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                                @endrole
                                <td>
                                    <span>{{ $product->brand->name??'N/A' }}</span>

                                </td>
                                <td>
                                    <span>{{ $product->category->name }}</span>

                                </td>
                                <td>
                                    @if($product->inventory_balance <= $product->inventory_threshold)
                                        <div class="d-flex flex-column align-items-center">
                                            <span class="badge bg-danger">
                                                <i class="bi bi-exclamation-triangle me-1"></i>
                                                {{ $product->inventory_balance }}
                                            </span>
                                            <small class="text-danger mt-1">Low Stock</small>
                                        </div>
                                        @else
                                        <span class="badge bg-success">{{ $product->inventory_balance }}</span>
                                        @endif
                                </td>

                                <td>
                                    <div class="d-flex flex-column align-items-center">
                                        <strong class="text-muted">{{ $product->sale_price  }}</strong>
                                    </div>
                                </td>
                                @role(['Admin','Supervisor'])

                                <td>
                                    <div class="d-flex flex-column align-items-center">
                                        <strong class="text-muted">{{ $product->purchase_price  }}</strong>
                                    </div>
                                </td>
                                @endrole



                                <td>
                                    <a target="_blank" href="{{ route('admin.barcode.print',$product->id) }}"

                                        class="btn btn-primary">
                                        <i class="bi bi-file-earmark-arrow-down "></i>
                                    </a>
                                    @role(['Admin','Supervisor'])

                                    <x-livewire.partials.edit-delete-form route="products" id="{{$product->id}}" />
                                    @endrole

                                </td>

                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-3 d-flex justify-content-between align-items-center">

                    {{ $products->links() }}
                </div>

            </x-livewire.partials.empty-result-form>
            @else
            <div class="text-center py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="text-muted mt-3">Loading products...</p>
            </div>
            @endif
        </div>
    </div>
</div>