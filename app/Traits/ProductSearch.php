<?php

namespace App\Traits;

use App\Models\Product;
use Illuminate\Support\Facades\DB;

trait ProductSearch
{

    //this is responsible for managing or showing the system inventory in inventory balance by precaculating it
    public function productSearch()
    {
        $search = $this->productSearch;

        $products = Product::query()
            ->select([
                'products.*',
             
                DB::raw('GREATEST(
                    COALESCE((
                        SELECT SUM(aptl.quantity)
                        FROM add_products_to_list aptl
                        INNER JOIN add_products ap ON ap.id = aptl.add_product_id
                        WHERE aptl.product_id = products.id
                        AND ap.status = "approved"
                        AND ap.deleted_at IS NULL
                    ), 0) +
                    COALESCE((
                        SELECT SUM(uttl.quantity)
                        FROM unsuccessful_transactions_to_list uttl
                        INNER JOIN unsuccessful_transactions ut ON ut.id = uttl.unsuccessful_transaction_id
                        WHERE uttl.product_id = products.id
                        AND ut.status = "approved"
                    ), 0) +
                    COALESCE((
                        SELECT SUM(pr.quantity)
                        FROM product_return pr
                        INNER JOIN return_items ri ON ri.id = pr.return_item_id
                        WHERE pr.product_id = products.id
                        AND ri.status = "approved"
                        AND ri.deleted_at IS NULL
                    ), 0) -
                    COALESCE((
                        SELECT SUM(ps.quantity)
                        FROM product_sale ps
                        INNER JOIN sales s ON s.id = ps.sale_id
                        WHERE ps.product_id = products.id
                        AND s.deleted_at IS NULL
                    ), 0) -
                    COALESCE((
                        SELECT SUM(op.quantity)
                        FROM order_product op
                        INNER JOIN orders o ON o.id = op.order_id
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
                ) as inventory_balance')
            ])
            ->when($search, function ($q) use ($search) {
                $q->where(function ($sub) use ($search) {
                    $sub->where('products.name', 'like', "%{$search}%")
                        ->orWhere('products.sale_price', 'like', "%{$search}%")
                        ->orWhere('products.location', 'like', "%{$search}%")
                        ->orWhere('products.barcode', $search);
                });
            })
            ->limit(4)
            ->get();

        return $products;
    }
}