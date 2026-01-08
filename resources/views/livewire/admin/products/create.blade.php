    <x-livewire.products-form header='Create new Product'
        :productCategories='$productCategories'
        :brands='$brands'
        :suppliers='$suppliers'
        :units='$units'>
        {{--<div class="mb-3 col-md-6">
            <div class="mb-3">
                <label class="form-label">Manual</label>
                <input type="file" wire:model="manual_image" class="form-control">

                <div wire:loading wire:target="manual_image" class="text-info mt-1">
                    Uploading...
                </div>

                @error('manual_image')
                <small class="form-text text-danger">{{ $message }}</small>
                @enderror

                <div class="mt-2">
                    @if($manual_image)
                    <img width="150" class="img-fluid p-2 border border-secondary"
                        src="{{ $manual_image->temporaryUrl() }}">
                    @elseif($product && $product->technical_path)
                    <img width="150" class="img-fluid p-2 border border-secondary"
                        src="{{ asset('storage/' . $product->technical_path) }}">
                    @endif
                </div>
            </div>
        </div>
--}}

    </x-livewire.products-form>