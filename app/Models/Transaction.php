<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $table = 'transactions';
    protected $primaryKey = 'id';
    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'invoice_id',       // ✅ TAMBAHKAN INI
        'product_type',     // ✅ TAMBAHKAN INI
        'user_id',
        'product_id',
        'quantity',
        'transaction_date',
        'amount',
        'validated_by',
        'status',
        'payment_proof',
    ];

    protected $casts = [
        'transaction_date' => 'datetime',
        'amount' => 'decimal:2',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function getItemAttribute()
    {
        // 1. If polymorphic product_type is explicitly set
        if (!empty($this->product_type) && class_exists($this->product_type)) {
            $class = $this->product_type;
            return $class::find($this->product_id);
        }

        // 2. Check if this transaction is linked to a Membership (for transactions without product_type)
        $membership = \App\Models\Membership::where('transaction_id', $this->id)->with('package')->first();
        if ($membership && $membership->package) {
            return $membership->package;
        }

        // 3. Default to Product
        return \App\Models\Product::find($this->product_id);
    }

    public function validator()
    {
        return $this->belongsTo(User::class, 'validated_by');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeValidated($query)
    {
        return $query->where('status', 'validated');
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    // Helper methods
    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isValidated()
    {
        return $this->status === 'validated';
    }

    public function isCancelled()
    {
        return $this->status === 'cancelled';
    }
}
