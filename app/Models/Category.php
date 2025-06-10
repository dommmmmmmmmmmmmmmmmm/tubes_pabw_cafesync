<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'slug', 'description', 'image', 'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];


    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($category) {
            $category->slug = Str::slug($category->name);
        });
        
        static::updating(function ($category) {
            $category->slug = Str::slug($category->name);
        });
    }

    public function menus()
    {
        return $this->hasMany(Menu::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
