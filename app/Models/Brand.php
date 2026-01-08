<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Brand extends Model
{
use HasFactory;

    // protected $appends = [
    //     'logo_url',
    // ];

    function products()
    {
        return $this->hasMany(Product::class);
    }

    // function getLogoUrlAttribute()
    // {
    //     return $this->logo_path ?? $this->defaultProfilePhotoUrl();
    // }

    // protected function defaultProfilePhotoUrl()
    // {
    //     $name = trim(collect(explode(' ', $this->name))->map(function ($segment) {
    //         return mb_substr($segment, 0, 1);
    //     })->join(' '));

    //     return 'https://ui-avatars.com/api/?name=' . urlencode($name) . '&color=7F9CF5&background=EBF4FF';
    // }
}