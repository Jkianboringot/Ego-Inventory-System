<?php

namespace App\Livewire\Admin\Products;

use App\Models\Product;
use App\Models\Supplier;
use App\Models\ProductCategory;
use App\Models\Brand;
use App\Traits\LoadData;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Storage;

class Index extends Component
{
    use WithPagination;
    use LoadData;

    public string $search = '';
    public string $supplierFilter = '';
    public string $categoryFilter = '';
    public string $brandFilter = '';

    protected $paginationTheme = 'bootstrap';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingSupplierFilter()
    {
        $this->resetPage();
    }

    public function updatingCategoryFilter()
    {
        $this->resetPage();
    }

    public function updatingBrandFilter()
    {
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->search = '';
        $this->supplierFilter = '';
        $this->categoryFilter = '';
        $this->brandFilter = '';
        $this->resetPage();
    }

    public function delete($id)
    {
        try {
            $product = Product::findOrFail($id);

            // Check if product has any transactions using exists() for better performance
            if ($product->purchases()->exists() || 
                $product->sales()->exists() ||
                $product->add_products()->exists() ||
                $product->returns()->exists()) {
                throw new \Exception("Permission: This product has been bought and/or sold.");
            }

            //  if ($product->technical_path && Storage::disk('public')->exists($product->technical_path)) {
            //     Storage::disk('public')->delete($product->technical_path);
            // }

            $product->delete();
            $this->dispatch('done', success: "Successfully Deleted.");
        } catch (\Throwable $th) {
            $this->dispatch('done', error: "Something went wrong: " . $th->getMessage());
        }
    }

    public function render()
    {
        $search = trim($this->search);

        $products = Product::query()
            ->select([
                'products.id',
                'products.name',
                'products.barcode',
                'products.location',
                'products.description',
                'products.supplier_id',
                'products.inventory_threshold',
                'products.brand_id',
                'products.product_category_id',
                'products.unit_id',
                'products.sale_price',
                'products.purchase_price',
                // 'products.technical_path',
                'products.created_at'
            ])
            ->selectRaw("
                GREATEST(
                    COALESCE((
                        SELECT SUM(apl.quantity)
                        FROM add_products_to_list apl
                        JOIN add_products ap ON ap.id = apl.add_product_id
                        WHERE apl.product_id = products.id
                        AND ap.status = 'approved'
                        AND ap.deleted_at IS NULL
                    ), 0)
                    + COALESCE((
                        SELECT SUM(utl.quantity)
                        FROM unsuccessful_transactions_to_list utl
                        JOIN unsuccessful_transactions ut ON ut.id = utl.unsuccessful_transaction_id
                        WHERE utl.product_id = products.id
                        AND ut.status = 'approved'
                    ), 0)
                    + COALESCE((
                        SELECT SUM(pr.quantity)
                        FROM product_return pr
                        JOIN return_items ri ON ri.id = pr.return_item_id
                        WHERE pr.product_id = products.id
                        AND ri.status = 'approved'
                        AND ri.deleted_at IS NULL
                    ), 0)
                    - COALESCE((
                        SELECT SUM(ps.quantity)
                        FROM product_sale ps
                        JOIN sales s ON s.id = ps.sale_id
                        WHERE ps.product_id = products.id
                        AND s.deleted_at IS NULL
                    ), 0)
                    - COALESCE((
                        SELECT SUM(op.quantity)
                        FROM order_product op
                        JOIN orders o ON o.id = op.order_id
                        WHERE op.product_id = products.id
                        AND o.deleted_at IS NULL
                    ), 0)-
                    COALESCE((
                        SELECT SUM(dl.quantity)
                        FROM defecteds_to_list dl
                        INNER JOIN defecteds d ON d.id = dl.defected_id
                        WHERE dl.product_id = products.id
                        AND d.deleted_at IS NULL
                    ), 0),
                0
            ) as inventory_balance
            ")
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('products.name', 'like', "%{$search}%")
                      ->orWhere('products.location', 'like', "%{$search}%")
                      ->orWhere('products.sale_price', 'like', "%{$search}%")
                      ->orWhere('products.barcode', $search)
                      ->orWhereHas('category', fn($q2) => $q2->where('name', 'like', "%{$search}%"))
                      ->orWhereHas('brand', fn($q2) => $q2->where('name', 'like', "%{$search}%"))
                      ->orWhereHas('supplier', fn($q2) => $q2->where('name', 'like', "%{$search}%"));
                });
            })
            ->when($this->supplierFilter, function ($query) {
                $query->where('products.supplier_id', $this->supplierFilter);
            })
            ->when($this->categoryFilter, function ($query) {
                $query->where('products.product_category_id', $this->categoryFilter);
            })
            ->when($this->brandFilter, function ($query) {
                $query->where('products.brand_id', $this->brandFilter);
            })
            ->with([
                'category:id,name',
                'unit:id,name',
                'supplier:id,name',
                'brand:id,name'
            ])
            ->orderBy('products.created_at', 'desc')
            ->simplePaginate(50);

        $suppliers = Supplier::select('id', 'name')
            ->orderBy('name')
            ->get();

        $categories = ProductCategory::select('id', 'name')
            ->orderBy('name')
            ->get();

        $brands = Brand::select('id', 'name')
            ->orderBy('name')
            ->get();

        return view('livewire.admin.products.index', [
            'products' => $products,
            'suppliers' => $suppliers,
            'categories' => $categories,
            'brands' => $brands,
        ]);
    }
}