<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Blade;

class PermissionServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $permissions = config('permissions.permissions', []);

        foreach ($permissions as $permission) {
            Gate::define($permission, function ($user) use ($permission) {
                return $user->hasPermission($permission);
            });
        }

        if (auth()->check()) {
            auth()->user()->load('roles');
        }

        Blade::if('role', function ($roles) {
            if (!auth()->check()) {
                return false;
            }

            if (is_string($roles)) {
                return auth()->user()->hasPermission($roles);
            }

            if (is_array($roles)) {
                foreach ($roles as $role) {
                    if (auth()->user()->hasPermission($role)) {
                        return true;
                    }
                }
                return false;
            }

            return false;
        });
        
    }
}