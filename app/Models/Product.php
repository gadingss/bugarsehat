<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // <-- Tambahkan ini

class Product extends Model
{
    use HasFactory;
    
    protected $table = 'products';
    protected $primaryKey = 'id';
    public $timestamps = true;
    
    protected $fillable = [
        'user_id', // <-- 1. TAMBAHKAN INI
        'name',
        'description',
        'price',
        'category',
        'stock',
        'image',
        'is_active',
        'is_promo',
        'promo_price',
        'promo_start_date',
        'promo_end_date',
        'status', // Tambahkan 'status' jika ada untuk approval (pending, approved)
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'promo_price' => 'decimal:2',
        'promo_start_date' => 'date',
        'promo_end_date' => 'date',
        'is_active' => 'boolean',
        'is_promo' => 'boolean',
        'stock' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // ==========================================================
    // Relationships
    // ==========================================================

    /**
     * Relasi untuk mendapatkan user (member) yang memiliki produk ini.
     */
    public function user(): BelongsTo // <-- 2. TAMBAHKAN FUNGSI RELASI INI
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function membershipProducts()
    {
        return $this->hasMany(MembershipProduct::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopePromo($query)
    {
        return $query->where('is_promo', true)
                     ->where('promo_start_date', '<=', now())
                     ->where('promo_end_date', '>=', now());
    }

    public function scopeInStock($query)
    {
        return $query->where('stock', '>', 0);
    }

    // Helper methods
    public function isOnPromo()
    {
        return $this->is_promo && 
               $this->promo_start_date <= now() && 
               $this->promo_end_date >= now();
    }

    public function getCurrentPrice()
    {
        return $this->isOnPromo() ? $this->promo_price : $this->price;
    }

    public function getDiscountPercentage()
    {
        if (!$this->isOnPromo()) {
            return 0;
        }
        
        return round((($this->price - $this->promo_price) / $this->price) * 100);
    }

    public function isInStock()
    {
        return $this->stock > 0;
    }
}