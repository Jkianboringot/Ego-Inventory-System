@props(['header','productCategories','brands','suppliers','units'])

<div>
    <x-livewire.partials.cancel-buttons />

    <x-slot:header>Products</x-slot:header>

    <div class="card" tabindex="0" wire:keydown.escape="cancel">

        <div class="card-header bg-inv-primary text-inv-secondary border-0">
            <h5>{{ $header }}</h5>
        </div>

        <form wire:submit.prevent="save">


            <!-- Disable whole form during upload -->

                <div class="card-body">
                    <div class="row g-3">

                        <!-- CATEGORY -->
                        <div class="col-md-6 col-12">
                            <label class="form-label">Product Category</label>
                            <select wire:model.defer='product.product_category_id' class="form-select" >
                                <option value="" selected>Select Category</option>
                                @foreach ($productCategories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                            @error('product.product_category_id')
                            <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <!-- BRAND -->
                        <div class="col-md-6 col-12">
                            <label class="form-label">Product Brand</label>
                            <select wire:model.defer='product.brand_id' class="form-select" >
                                <option value="" selected>Select Brand</option>
                                @foreach ($brands as $brand)
                                <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                                @endforeach
                            </select>
                            @error('product.brand_id')
                            <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <!-- NAME -->
                        <div class="col-md-6 col-12">
                            <label class="form-label">Name</label>
                            <input wire:model.defer='product.name' type="text" class="form-control" placeholder="Product's Name"
                                maxlength="75" minlength="3" required>
                            @error('product.name')
                            <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <!-- LOCATION -->
                        <div class="col-md-6 col-12">
                            <label class="form-label">Shelf Location</label>
                            <input wire:model.defer='product.location' type="text" class="form-control" placeholder="Shelf Location (e.g A1-03)"
                                maxlength="20" minlength="1">
                            @error('product.location')
                            <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <!-- BARCODE -->
                        <div class="col-md-6 col-12">
                            <label class="form-label">Barcode</label>
                            <input wire:model.defer='product.barcode' type="text" class="form-control" placeholder="Enter product's barcode (49203943)"
                                maxlength="30" minlength="3" required>
                            @error('product.barcode')
                            <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <!-- SUPPLIER -->
                        <div class="col-md-6 col-12">
                            <label class="form-label">Supplier</label>
                            <select wire:model.defer='product.supplier_id' class="form-select" >
                                <option value="" selected>Select Supplier</option>
                                @foreach ($suppliers as $supplier)
                                <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                                @endforeach
                            </select>
                            @error('product.supplier_id')
                            <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <!-- DESCRIPTION -->
                        <div class="col-12">
                            <label class="form-label">Product Description</label>
                            <textarea wire:model.defer='product.description' class="form-control" rows="3"
                                maxlength="600" minlength="15" placeholder="Add product description (optional)"></textarea>
                            @error('product.description')
                            <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <!-- IMAGE UPLOAD -->

                        <!-- THRESHOLD -->
                        <div class="col-md-6 col-12">
                            <label class="form-label">Product Threshold</label>
                            <input wire:model.defer='product.inventory_threshold' type="number" step="1" class="form-control"
                                max="10000" min="1" placeholder="Enter threshold to Alert product understock (e.g 10)" required>
                            @error('product.inventory_threshold')
                            <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <!-- UNIT -->
                        <div class="col-md-6 col-12">
                            <label class="form-label">Product Measure Unit</label>
                            <select wire:model.defer='product.unit_id' class="form-select" required>
                                <option value="" selected>Select Unit</option>
                                @foreach ($units as $unit)
                                <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                                @endforeach
                            </select>
                            @error('product.unit_id')
                            <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>



                        <!-- PURCHASE PRICE -->
                        <div class="col-md-6 col-12">
                            <label class="form-label">Purchase Price</label>
                            <input wire:model.defer='product.purchase_price' type="number" step="0.1" min="1" class="form-control"
                                max="999999.99" placeholder="Enter product's purchase price" required>
                            @error('product.purchase_price')
                            <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        {{ $slot }}

                        <!-- SALE PRICE -->
                        <div class="col-md-6 col-12">
                            <label class="form-label">Selling Price</label>
                            <input wire:model.defer='product.sale_price' type="number" step="0.1" min="1" class="form-control"
                                max="999999.99" placeholder="Enter product's selling price" required>
                            @error('product.sale_price')
                            <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>


                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-3">
                        <x-livewire.partials.cancelProcess-button />
                        <x-livewire.partials.save-buttons />

                    </div>
        </form>
        <!-- SAVE BUTTON -->
        <!-- <button class="btn btn-dark text-inv-secondary"

                wire:loading.attr="disabled"
                wire:target="manual_image">
                <span wire:loading wire:target="save">Saving...</span>
                <span wire:loading.remove wire:target="save">Save</span>
            </button> -->
    </div>



    </form>
</div>
</div>