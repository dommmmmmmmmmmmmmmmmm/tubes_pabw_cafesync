<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Table extends Model
{
    use HasFactory;

    protected $fillable = [
        'table_number', 'qr_code', 'capacity', 'status'
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($table) {
            $table->qr_code = Str::random(32);
        });
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function currentOrder()
    {
        return $this->hasOne(Order::class)->whereNotIn('status', ['selesai', 'dibatalkan']);
    }

    public function scopeAvailable($query)
    {
        return $query->where('status', 'available');
    }
}