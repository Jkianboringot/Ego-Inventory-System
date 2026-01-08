<?php

namespace App\Livewire\Admin\Products;

use App\Models\Brand;
use App\Models\Supplier;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Unit;
use App\Traits\WithCancel;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class Create extends Component
{
    use WithCancel, WithFileUploads;

    public Product $product;
    public $context = 'products';

    protected function rules()
    {
        return [
            'product.name' => [
                'required',
                'max:75','min:5',
                Rule::unique('products', 'name')->whereNull('deleted_at')
            ],
            'product.brand_id' => 'required|exists:brands,id',
            'product.supplier_id' => 'required|exists:suppliers,id',
            'product.description' => 'nullable|string|max:600|min:15',
            'product.unit_id' => 'required|exists:units,id',
            'product.product_category_id' => 'required|exists:product_categories,id',
            'product.purchase_price' => 'required|numeric|min:1|max:999999.99',
            'product.sale_price' => 'required|numeric|min:1|max:999999.99',
            'product.location' => 'nullable|max:20|min:3',
            'product.barcode' => [
                'required',
                'string',
                'max:30','min:5',
                Rule::unique('products', 'barcode')->whereNull('deleted_at')
            ],
            'product.inventory_threshold' => 'required|integer|min:1|max:10000',
        ];
    }

    public function mount()
    {
        $this->product = new Product();
    }

    // REMOVE the updated() method entirely - it's causing issues
    
    public function save()
    {
        // Validate only on save
        $this->validate();

        try {
            DB::Transaction(function() {

           
            $existingProduct = Product::where('barcode', $this->product->barcode)
                ->orWhere('name', $this->product->name)
                ->exists();

            if ($existingProduct) {
                throw new \Exception('A product with this name or barcode already exists.');
            }

            $this->product->save();


                });

            return redirect()->route('admin.products.index')
            ->with('success','Product Created.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            throw $e; 
            
        } catch (\Throwable $th) {
            DB::rollBack();
            $this->dispatch('done', error: "Something went wrong: " . $th->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.admin.products.create', [
            'productCategories' => ProductCategory::select(['id', 'name'])->orderBy('name')->get(),
            'units' => Unit::select(['id', 'name'])->orderBy('name')->get(),
            'brands' => Brand::select(['id', 'name'])->orderBy('name')->get(),
            'suppliers' => Supplier::select(['id', 'name'])->orderBy('name')->get()
        ]);
    }
}