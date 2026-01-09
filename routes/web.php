<?php

use App\Models\Order;
use Barryvdh\DomPDF\Facade\PDF;
use Database\Factories\OrderFactory;
use Illuminate\Support\Facades\Route;
use App\Models\Product;
use Picqer\Barcode\BarcodeGeneratorPNG;

Route::get('/', function () {
    return redirect('dashboard');
});


Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->name('admin.')->group(function () {

    Route::get('/product/{id}/barcode', function ($id) {
        $product = Product::findOrFail($id);

        $generator = new BarcodeGeneratorPNG();
        $barcodeNumber = $product->barcode ?? str_pad($product->id, 8, '0', STR_PAD_LEFT);

        // Reduce barcode height to fit label
        $barcodeImage = base64_encode($generator->getBarcode($barcodeNumber, $generator::TYPE_CODE_128, 3, 30));

        return PDF::loadView('pdf.barcode-label', [
            'product' => $product,
            'barcodeImage' => $barcodeImage
        ])
            ->setPaper([0, 0, 170.1, 113.4], 'portrait')
            ->setOption('disable-smart-shrinking', true)
            ->setOption('margin-top', 0)
            ->setOption('margin-bottom', 0)
            ->setOption('margin-left', 0)
            ->setOption('margin-right', 0)
            ->stream('barcode-' . $product->id . '.pdf');
    })->name('barcode.print');


    // Bulk barcode print (multiple products on 50.8mm x 76.2mm stickers)
    Route::post('/products/barcode/bulk', function (Illuminate\Http\Request $request) {
        $productIds = $request->input('product_ids', []);

        // Decode if sent as JSON string
        if (is_string($productIds)) {
            $productIds = json_decode($productIds, true);
        }

        $products = Product::whereIn('id', $productIds)->get();

        // Generate barcodes for all products
        $generator = new BarcodeGeneratorPNG();
        $productsWithBarcodes = $products->map(function ($product) use ($generator) {
            $barcodeNumber = $product->barcode ?? str_pad($product->id, 8, '0', STR_PAD_LEFT);
            $product->barcodeImage = base64_encode($generator->getBarcode($barcodeNumber, $generator::TYPE_CODE_128, 3, 50));
            return $product;
        });

        return PDF::loadView('pdf.barcode-label-bulk', [
            'products' => $productsWithBarcodes
        ])
            ->setPaper([0, 0, 144, 216], 'portrait') // 50.8mm x 76.2mm sticker size
            ->stream('barcodes-bulk.pdf');
    })->name('barcode.bulk.print');
    Route::middleware(['cache.headers:public;max_age=2628000;etag'])->group(function () {
        Route::get('/admin-lte/{path}', function ($path) {
            return response()->file(public_path('admin-lte/' . $path));
        })->where('path', '.*');
    });






    Route::get('/dashboard', \App\Livewire\Admin\Dashboard::class)->name('dashboard');



    Route::prefix('users')->middleware('permission:Supervisor,Admin')->name('users.')->group(function () {
        Route::get('/', \App\Livewire\Admin\Users\Index::class)->name('index');
        Route::get('/create', \App\Livewire\Admin\Users\Create::class)->name('create');
        Route::get('{id}/edit', \App\Livewire\Admin\Users\Edit::class)->name('edit');
    });



    Route::prefix('brands')->middleware('permission:Supervisor,Admin')->name('brands.')->group(function () {
        Route::get('/', \App\Livewire\Admin\Brands\Index::class)->name('index');
        Route::get('/create', \App\Livewire\Admin\Brands\Create::class)->name('create');
        Route::get('{id}/edit', \App\Livewire\Admin\Brands\Edit::class)->name('edit');
    });

    Route::prefix('customers')->middleware('permission:Sales Clerk,Supervisor,Admin')->name('customers.')->group(function () {
        Route::get('/', \App\Livewire\Admin\Customers\Index::class)->name('index');
        Route::get('/create', \App\Livewire\Admin\Customers\Create::class)->name('create');
        Route::get('{id}/edit', \App\Livewire\Admin\Customers\Edit::class)->name('edit');
    });

    Route::prefix('returns')->middleware('permission:Return and Exchange Clerk,Supervisor,Admin')->name('returns.')->group(function () {
        Route::get('/', \App\Livewire\Admin\Returns\Index::class)->name('index');
        Route::get('/create', \App\Livewire\Admin\Returns\Create::class)->name('create');
        Route::get('{id}/edit', \App\Livewire\Admin\Returns\Edit::class)->name('edit');
    });


    Route::prefix('orders')->middleware('permission:Warehouse Keeper,Supervisor,Admin')->name('orders.')->group(function () {
        Route::get('/', \App\Livewire\Admin\Orders\Index::class)->name('index');
        Route::get('/create', \App\Livewire\Admin\Orders\Create::class)->name('create');
        Route::get('{id}/edit', \App\Livewire\Admin\Orders\Edit::class)->name('edit');
    });

    Route::prefix('product-categories')->middleware('permission:Supervisor,Admin')->name('productcategories.')->group(function () {
        Route::get('/', \App\Livewire\Admin\ProductCategories\Index::class)->name('index');
        Route::get('/create', \App\Livewire\Admin\ProductCategories\Create::class)->name('create');
        Route::get('{id}/edit', \App\Livewire\Admin\ProductCategories\Edit::class)->name('edit');
    });

    Route::prefix('products')->middleware('permission:Supervisor,Admin,Inventory Clerk')->name('products.')->group(function () {
        Route::get('/', \App\Livewire\Admin\Products\Index::class)->name('index');
        Route::get('/create', \App\Livewire\Admin\Products\Create::class)->middleware('permission:Supervisor,Admin')->name('create');
        Route::get('{id}/edit', \App\Livewire\Admin\Products\Edit::class)->middleware('permission:Supervisor,Admin')->name('edit');
    });

    Route::prefix('purchases')->middleware('permission:Supervisor,Admin')->name('purchases.')->group(function () {
        Route::get('/', \App\Livewire\Admin\Purchases\Index::class)->name('index');
        Route::get('/create', \App\Livewire\Admin\Purchases\Create::class)->name('create');
        Route::get('{id}/edit', \App\Livewire\Admin\Purchases\Edit::class)->name('edit');
        Route::get('unsettled-products', \App\Livewire\Admin\Purchases\UnsettledPurchases::class)->name('unsettled');
    });
    Route::prefix('add-products')
        ->middleware('permission:Inventory Clerk,Supervisor,Admin')
        ->name('add-products.')
        ->group(function () {
            Route::get('/', \App\Livewire\Admin\AddProducts\Index::class)->name('index');
            Route::get('/create', \App\Livewire\Admin\AddProducts\Create::class)->name('create');
            Route::get('{id}/edit', \App\Livewire\Admin\AddProducts\Edit::class)->name('edit');
        });



    Route::prefix('unsuccessful-transactions')
        ->middleware('permission:Warehouse Keeper,Supervisor,Admin')
        ->name('unsuccessful-transactions.')
        ->group(function () {
            Route::get('/', \App\Livewire\Admin\UnsuccessfulTransactions\Index::class)->name('index');
            Route::get('/create', \App\Livewire\Admin\UnsuccessfulTransactions\Create::class)->name('create');
            Route::get('{id}/edit', \App\Livewire\Admin\UnsuccessfulTransactions\Edit::class)->name('edit');
        });

    Route::prefix('defected')
        ->middleware('permission:Supervisor,Admin')
        ->name('defected.')
        ->group(function () {
            Route::get('/', \App\Livewire\Admin\Defected\Index::class)->name('index');
            Route::get('/create', \App\Livewire\Admin\Defected\Create::class)->name('create');
            Route::get('{id}/edit', \App\Livewire\Admin\Defected\Edit::class)->name('edit');
        });

    Route::prefix('approvals')
        ->middleware('permission:Supervisor,Admin')
        ->name('approvals.')
        ->group(function () {
            //   Route::get('/addapproval', Admin\Approvals\AddApproval::class)->name('addapproval');
            // Route::get('/approvaledit', Admin\Approvals\ApprovalEdit::class)->name('approvaledit');
            Route::get('/approvalcenter', \App\Livewire\Admin\Approvals\ApprovalCenter::class)->name('approvalcenter');
        });





    Route::prefix('roles')->middleware('permission:Supervisor,Admin')->name('roles.')->group(function () {
        Route::get('/', \App\Livewire\Admin\Roles\Index::class)->name('index');
        // Route::get('/create', Admin\Roles\Create::class)->name('create');
        // Route::get('{id}/edit', Admin\Roles\Edit::class)->name('edit');
    });
    Route::prefix('sales')->middleware('permission:Sales Clerk,Supervisor,Admin')->name('sales.')->group(function () {
        Route::get('/', \App\Livewire\Admin\Sales\Index::class)->name('index');
        Route::get('/create', \App\Livewire\Admin\Sales\Create::class)->name('create');
        Route::get('{id}/edit', \App\Livewire\Admin\Sales\Edit::class)->name('edit');
    });

    Route::prefix('suppliers')->middleware('permission:Supervisor,Admin')->name('suppliers.')->group(function () {
        Route::get('/', \App\Livewire\Admin\Suppliers\Index::class)->name('index');
        Route::get('/create', \App\Livewire\Admin\Suppliers\Create::class)->name('create');
        Route::get('{id}/edit', \App\Livewire\Admin\Suppliers\Edit::class)->name('edit');
    });

    Route::prefix('units')->middleware('permission:Supervisor,Admin')->name('units.')->group(function () {
        Route::get('/', \App\Livewire\Admin\Units\Index::class)->name('index');
        Route::get('/create', \App\Livewire\Admin\Units\Create::class)->name('create');
        Route::get('{id}/edit', \App\Livewire\Admin\Units\Edit::class)->name('edit');
    });

    Route::prefix('activity-logs')->middleware('permission:Supervisor,Admin')->name('activity-logs.')->group(function () {
        Route::get('/', \App\Livewire\Admin\ActivityLogs\Index::class)
            ->name('index');
    });

    // Route::prefix('defected-products')->middleware('permission:manage activity logs')->name('defected-products.')->group(function () {
    //     Route::get('/', \App\Livewire\Admin\DefectedProduct::class)
    //         ->name('defects');
    // });
});
