<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MembershipPacket extends Model
{
    use HasFactory;
    
    protected $table = 'membership_packages';
    protected $primaryKey = 'id';
    public $timestamps = true;
    
    protected $fillable = [
        'name',
        'price',
        'duration_days',
        'max_visits',
        'description',
        'product_details',
        'is_active',
        'type'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'duration_days' => 'integer',
        'max_visits' => 'integer',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function memberships()
    {
        return $this->hasMany(Membership::class, 'package_id');
    }

    public function activeMemberships()
    {
        return $this->hasMany(Membership::class, 'package_id')->where('status', 'active');
    }

    public function services()
    {
        return $this->belongsToMany(Service::class, 'membership_packet_services', 'membership_packet_id', 'service_id')
                    ->withTimestamps();
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Helper methods
    public function isActive()
    {
        return $this->is_active ?? true;
    }

    public function getFormattedPriceAttribute()
    {
        return 'Rp ' . number_format($this->price, 0, ',', '.');
    }
}
