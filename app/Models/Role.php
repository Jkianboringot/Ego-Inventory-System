<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Role extends Model
{
    use SoftDeletes;

    protected $guarded=['id']; 
        protected $fillable = ['title','permissions'];
protected $casts = [
    'permissions' => 'array', 
];
    
       function users(){
        return $this->belongsToMany(User::class,'role_user');
    }

    protected static function booted()
    {
        static::updated(function ($role) {
            $role->users()->each(fn($user) => $user->clearPermissionCache());
        });

        static::deleted(function ($role) {
            $role->users()->each(fn($user) => $user->clearPermissionCache());
        });
    }   
}
