@props(['title','route'])

<div class="card-header">

    <!-- TITLE + CREATE BUTTON -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="text-white fw-semibold mb-0">{{ $title }} list</h5>

        <form action="{{ route('admin.'.$route.'.create') }}" onsubmit="showLoading(event)">
            <button type="submit" class="btn-inv-secondary" id="createBtn">
                <span id="btnText">
                    <i class="bi bi-plus-circle me-1"></i>Create
                </span>
                <span id="btnLoading" style="display:none;">
                    <i class="bi bi-arrow-repeat spin me-1"></i>Loading...
                </span>
            </button>
        </form>
    </div>

    <!-- FILTERS -->
    <div class="row g-2">
        @role(['Admin','Supervisor'])
        <div class="col-md-3">
            <label class="form-label text-white">
                <i class="bi bi-truck me-1"></i>Supplier
            </label>
            <select wire:model.live="supplierFilter" class="form-select">
                <option value="">All Suppliers</option>
                @foreach($suppliers as $supplier)
                    <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                @endforeach
            </select>
        </div>
        @endrole

        <div class="col-md-3">
            <label class="form-label text-white">
                <i class="bi bi-folder me-1"></i>Category
            </label>
            <select wire:model.live="categoryFilter" class="form-select">
                <option value="">All Categories</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-3">
            <label class="form-label text-white">
                <i class="bi bi-tag me-1"></i>Brand
            </label>
            <select wire:model.live="brandFilter" class="form-select">
                <option value="">All Brands</option>
                @foreach($brands as $brand)
                    <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                @endforeach
            </select>
        </div>

        @if($search || $supplierFilter || $categoryFilter || $brandFilter)
        <div class="col-md-3 d-flex align-items-end">
            <button wire:click="resetFilters" class="btn btn-outline-secondary w-100">
                <i class="bi bi-x-circle me-1"></i>Reset
            </button>
        </div>
        @endif
    </div>
</div>

<script>
function showLoading() {
    document.getElementById('btnText').style.display = 'none';
    document.getElementById('btnLoading').style.display = 'inline-block';
}
</script>

<style>
.spin {
    animation: spin 1s linear infinite;
}
@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}
</style>
