<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // <-- Tambahkan ini

class Service extends Model
{
    use HasFactory;
    
    protected $table = 'additional_services';
    protected $primaryKey = 'id';
    public $timestamps = true;
    
    protected $fillable = [
        'user_id', // <-- 1. TAMBAHKAN INI
        'name',
        'description',
        'price',
        'duration_minutes',
        'category',
        'is_active',
        'requires_booking',
        'max_participants',
        'image',
        'status', // Tambahkan 'status' jika ada untuk approval (pending, approved)
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'duration_minutes' => 'integer',
        'max_participants' => 'integer',
        'is_active' => 'boolean',
        'requires_booking' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // ==========================================================
    // Relationships
    // ==========================================================
    
    /**
     * Relasi untuk mendapatkan user (member) yang memiliki layanan ini.
     */
    public function user(): BelongsTo // <-- 2. TAMBAHKAN FUNGSI RELASI INI
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function serviceTransactions()
    {
        return $this->hasMany(ServiceTransaction::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeRequiresBooking($query)
    {
        return $query->where('requires_booking', true);
    }

    // Helper methods
    public function isActive()
    {
        return $this->is_active;
    }

    public function requiresBooking()
    {
        return $this->requires_booking;
    }

    public function getFormattedDuration()
    {
        $hours = floor($this->duration_minutes / 60);
        $minutes = $this->duration_minutes % 60;
        
        if ($hours > 0 && $minutes > 0) {
            return $hours . ' jam ' . $minutes . ' menit';
        } elseif ($hours > 0) {
            return $hours . ' jam';
        } else {
            return $minutes . ' menit';
        }
    }
}