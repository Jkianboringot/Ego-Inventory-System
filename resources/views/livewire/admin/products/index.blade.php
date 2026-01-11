<div wire:init="loadData" x-data="{ descriptionModal: '', supplierModal: [] }">

    <div class="card shadow">

        <!-- HEADER -->
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">

                <!-- TITLE -->
                <h5 class="text-white fw-semibold mb-0">Product list</h5>

                <!-- FILTERS + CREATE BUTTON -->
                <div class="d-flex align-items-center gap-2">

                    @role(['Admin','Supervisor'])
                    <select wire:model.live="supplierFilter" class="form-select form-select-sm" style="width:150px;">
                        <option value="">All Suppliers</option>
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                        @endforeach
                    </select>
                    @endrole

                    <select wire:model.live="categoryFilter" class="form-select form-select-sm" style="width:150px;">
                        <option value="">All Categories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>

                    <select wire:model.live="brandFilter" class="form-select form-select-sm" style="width:150px;">
                        <option value="">All Brands</option>
                        @foreach($brands as $brand)
                            <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                        @endforeach
                    </select>

                    @if($search || $supplierFilter || $categoryFilter || $brandFilter)
                    <button wire:click="resetFilters" class="btn btn-outline-light btn-sm">
                        <i class="bi bi-x-circle me-1"></i>Reset
                    </button>
                    @endif

                    <form action="{{ route('admin.products.create') }}" onsubmit="showLoading(event)">
                        <button type="submit" class="btn-inv-secondary btn-sm" id="createBtn">
                            <span id="btnText">
                                <i class="bi bi-plus-circle me-1"></i>Create
                            </span>
                            <span id="btnLoading" style="display:none;">
                                <i class="bi bi-arrow-repeat spin me-1"></i>Loading...
                            </span>
                        </button>
                    </form>

                </div>
            </div>
        </div>

        <!-- BODY -->
        <div class="card-body">

            <x-livewire.partials.search-bar :$search />

            <!-- Filters Section (optional duplicate, can remove if already in header) -->
            <!-- ... keep your filters row here if needed ... -->

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
                                @role(['Admin','Supervisor','Tagger'])
                                <th>Actions</th>
                                @endrole
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($products as $product)
                            <tr class="text-center">
                                <td><strong>{{ $product->barcode }}</strong></td>
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
                                <td>{{ $product->brand->name ?? 'N/A' }}</td>
                                <td>{{ $product->category->name ?? 'N/A' }}</td>
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
                                <td><strong class="text-muted">{{ $product->sale_price }}</strong></td>
                                @role(['Admin','Supervisor'])
                                <td><strong class="text-muted">{{ $product->purchase_price }}</strong></td>
                                @endrole
                                <td>
                                    <a target="_blank" href="{{ route('admin.barcode.print',$product->id) }}" class="btn btn-primary">
                                        <i class="bi bi-file-earmark-arrow-down"></i>
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

        </div> <!-- END CARD BODY -->

    </div> <!-- END CARD -->

</div> <!-- END ROOT DIV -->

<script>
function showLoading() {
    document.getElementById('btnText').style.display = 'none';
    document.getElementById('btnLoading').style.display = 'inline-block';
}
</script>
