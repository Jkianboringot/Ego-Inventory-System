<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Cache;

class User extends Authenticatable
{
    
    use SoftDeletes;
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;

    protected $fillable = ['name', 'email', 'password'];

    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    // protected $appends = ['profile_photo_url'];

    // ✅ In-memory cache property
    protected $_permissionCache = null;

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_user');
    }

    public function plainText()
    {
        return $this->hasMany(PlainText::class, 'users_id');
    }

    /**
     * Check if user has a specific permission (with caching)
     */
    public function hasPermission($permission)
    {
        // ✅ LEVEL 1: Check in-memory cache first
        if ($this->_permissionCache === null) {
            // ✅ LEVEL 2: Check database cache
            $this->_permissionCache = Cache::remember(
                "user.{$this->id}.roles",
                1800, // 30 minutes
                function () {
                    return $this->roles;
                }
            );
        }

        // ✅ Loop through cached roles exactly like your original code
        foreach ($this->_permissionCache as $key => $role) {
            // ✅ FIX: Decode JSON string to array
            $permissions = is_string($role->permissions) 
                ? json_decode($role->permissions, true) 
                : ($role->permissions ?? []);
            
            if (in_array($permission, $permissions)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Clear permission cache
     */
    protected static function booted()
    {
        static::updated(fn($user) => $user->clearPermissionCache());
        static::deleted(fn($user) => $user->clearPermissionCache());
    }

    public function clearPermissionCache()
    {
        // Clear both in-memory and database cache
        $this->_permissionCache = null;
        Cache::forget("user.{$this->id}.roles");
    }
}