<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MembershipProduct extends Model
{
    use HasFactory;
    
    protected $table = 'membership_products';
    protected $primaryKey = 'id';
    public $timestamps = true;
    
    protected $fillable = [
        'membership_id',
        'product_id',
        'quantity',
        'used_quantity',
        'expires_at'
    ];

    protected $casts = [
        'quantity' => 'integer',
        'used_quantity' => 'integer',
        'expires_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function membership()
    {
        return $this->belongsTo(Membership::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Helper methods
    public function getRemainingQuantity()
    {
        return $this->quantity - $this->used_quantity;
    }

    public function isExpired()
    {
        return $this->expires_at && $this->expires_at < now();
    }

    public function canUse()
    {
        return $this->getRemainingQuantity() > 0 && !$this->isExpired();
    }

    public function useProduct($quantity = 1)
    {
        if ($this->canUse() && $this->getRemainingQuantity() >= $quantity) {
            $this->used_quantity += $quantity;
            $this->save();
            return true;
        }
        return false;
    }
}
