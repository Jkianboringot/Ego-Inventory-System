<?php

namespace App\Traits;

use App\Models\Product;
use Illuminate\Support\Facades\DB;

trait GetProductWithInventory
{
        //this is just responsible for pre loading the product inventory
   
    private function getProductsWithInventory($productIds)
    {
        if (empty($productIds)) return [];

        return Product::whereIn('id', $productIds)
            ->select(
                'products.id',
                'products.name',
                'products.inventory_threshold',
                DB::raw('
                    COALESCE((
                        SELECT SUM(add_products_to_list.quantity)
                        FROM add_products
                        INNER JOIN add_products_to_list ON add_products.id = add_products_to_list.add_product_id
                        WHERE add_products_to_list.product_id = products.id 
                        AND add_products.status = "approved"
                        AND add_products.deleted_at IS NULL
                    ), 0)
                    +
                    COALESCE((
                        SELECT SUM(product_return.quantity)
                        FROM return_items
                        INNER JOIN product_return ON return_items.id = product_return.return_item_id
                        WHERE product_return.product_id = products.id 
                        AND return_items.status = "approved"
                        AND return_items.deleted_at IS NULL
                    ), 0)
                    +
                    COALESCE((
                        SELECT SUM(unsuccessful_transactions_to_list.quantity)
                        FROM unsuccessful_transactions
                        INNER JOIN unsuccessful_transactions_to_list ON unsuccessful_transactions.id = unsuccessful_transactions_to_list.unsuccessful_transaction_id
                        WHERE unsuccessful_transactions_to_list.product_id = products.id 
                        AND unsuccessful_transactions.status = "approved"
                    ), 0)
                    -
                    COALESCE((
                        SELECT SUM(product_sale.quantity)
                        FROM sales
                        INNER JOIN product_sale ON sales.id = product_sale.sale_id
                        WHERE product_sale.product_id = products.id
                        AND sales.deleted_at IS NULL
                    ), 0)
                    -
                    COALESCE((
                        SELECT SUM(order_product.quantity)
                        FROM orders
                        INNER JOIN order_product ON orders.id = order_product.order_id
                        WHERE order_product.product_id = products.id
                        AND orders.deleted_at IS NULL
                    ), 0)-
                    COALESCE((
                        SELECT SUM(dl.quantity)
                        FROM defecteds_to_list dl
                        INNER JOIN defecteds d ON d.id = dl.defected_id
                        WHERE dl.product_id = products.id
                        AND d.deleted_at IS NULL
                    ), 0) as inventory_balance
                ')
            )
            ->get()
            ->keyBy('id')
            ->toArray();
    }
}
