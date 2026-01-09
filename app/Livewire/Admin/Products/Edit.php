<?php

namespace App\Livewire\Admin\Products;

use App\Models\Brand;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Supplier;
use App\Models\Unit;
use App\Models\ActivityLog;
use App\Traits\WithCancel;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class Edit extends Component
{
    use WithCancel, WithFileUploads;

    public Product $product;
    public $context = 'products'; // Was 'roles' - typo?

    // Store original quantity for comparison
    private $original;

    protected function rules()
    {
        return [
            'product.name' => [
                'required',
                'max:75','min:5',
                Rule::unique('products', 'name')
                    ->ignore($this->product->id)
                    ->whereNull('deleted_at')
            ],
            'product.brand_id' => 'nullable|exists:brands,id',
            'product.supplier_id' => 'required|exists:suppliers,id',
            'product.description' => 'nullable|string|max:600|min:15',
            'product.unit_id' => 'required|integer|exists:units,id',
            'product.product_category_id' => 'nullable|exists:product_categories,id',
            'product.purchase_price' => 'required|numeric|min:1|max:999999.99',
            'product.sale_price' => 'required|numeric|min:1|max:999999.99',
            'product.location' => 'nullable|string|max:20|min:3',
            'product.barcode' => [
                'required',
                'max:30','min:5',
                Rule::unique('products', 'barcode')
                    ->ignore($this->product->id)
                    ->whereNull('deleted_at')
            ],
            'product.inventory_threshold' => 'required|integer|min:1|max:1000',
        ];
    }

    public function mount($id)
    {
        $this->product = Product::findOrFail($id);

        $this->original = $this->product;
    }

    public function save()
    {
        $this->validate();

        try {

            $oldProduct = $this->original;
            $newProduct = $this->product;
            DB::Transaction(function () {
                
                $duplicate = Product::where('id', '!=', $this->product->id)
                    ->where(function ($query) {
                        $query->where('barcode', $this->product->barcode)
                            ->orWhere('name', $this->product->name);
                    })
                    ->whereNull('deleted_at')
                    ->exists();

                if ($duplicate) {
                    throw new \Exception('A product with this name or barcode already exists.');
                }

                $this->product->save();
            });
            $this->logQuantityChange($oldProduct, $newProduct);


            return redirect()->route('admin.products.index')
            ->with('success','Product Updated.');
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            throw $e;
        } catch (\Throwable $th) {
            DB::rollBack();
            $this->dispatch('done', error: "Something went wrong: " . $th->getMessage());
        }
    }


    private function logQuantityChange($oldProduct, $newProduct)
    {

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'Updated',
            'model' => 'Product',
            'changes' => json_encode([
                'old_quantity' => $oldProduct,
                'new_quantity' => $newProduct,
                'product_name' => $this->product->name,
            ]),
            'ip_address' => request()->ip(),
            'user_agent' => request()->header('User-Agent'),
        ]);
    }

    public function render()
    {
        return view('livewire.admin.products.edit', [
            'productCategories' => ProductCategory::select(['id', 'name'])->orderBy('name')->get(),
            'units' => Unit::select(['id', 'name'])->orderBy('name')->get(),
            'brands' => Brand::select(['id', 'name'])->orderBy('name')->get(),
            'suppliers' => Supplier::select(['id', 'name'])->orderBy('name')->get()
        ]);
    }
}
