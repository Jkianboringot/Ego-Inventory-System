<?php

namespace App\Providers;

use App\Models\ActivityLog;
use App\Models\AddProduct;
use App\Models\Brand;
use Illuminate\Support\ServiceProvider;
use App\Models\Product;
use App\Models\Purchase;

use App\Models\Sale;
use App\Models\Customer;
use App\Models\Order;
use App\Models\ProductCategory;
use App\Models\ReturnItem;
use App\Models\Role;
use App\Models\Supplier;
use App\Models\Unit;
use App\Models\User;
use App\Observers\ActivityObserver;
use Illuminate\Auth\Events\Logout;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Event;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */

    public function boot()
    {
        Product::observe(ActivityObserver::class);
        Customer::observe(ActivityObserver::class);
        Supplier::observe(ActivityObserver::class);
        User::observe(ActivityObserver::class);
        // AddProduct::observe(ActivityObserver::class);
        Role::observe(ActivityObserver::class);
        // Purchase::observe(ActivityObserver::class);
        Sale::observe(ActivityObserver::class);
        ReturnItem::observe(ActivityObserver::class);
        Order::observe(ActivityObserver::class);
        ProductCategory::observe(ActivityObserver::class);
        Unit::observe(ActivityObserver::class);
        Brand::observe(ActivityObserver::class);

        Event::listen(Logout::class, function ($event) {
            ActivityLog::create([
                'user_id' => $event->user->id,
                'action' => 'logout',
                'model' => 'User',
                'ip_address' => request()->ip(),
                'user_agent' => request()->header('User-Agent'),
            ]);
        });

        Event::listen(Login::class, function ($event) {
            ActivityLog::create([
                'user_id' => $event->user->id,
                'action' => 'login',
                'model' => 'User',
                'ip_address' => request()->ip(),
                'user_agent' => request()->header('User-Agent'),
            ]);
        });
    }
}
