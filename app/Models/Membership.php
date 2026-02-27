<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Membership extends Model
{
    use HasFactory;
    
    protected $table = 'memberships';
    protected $primaryKey = 'id';
    public $timestamps = true;
    
    protected $fillable = [
        'user_id',
        'package_id',
        'transaction_id',
        'type',
        'start_date',
        'end_date',
        'remaining_visits',
        'status'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'remaining_visits' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function package()
    {
        return $this->belongsTo(MembershipPacket::class, 'package_id');
    }

    public function transaction()
    {
        return $this->belongsTo(Transaction::class, 'transaction_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeExpired($query)
    {
        return $query->where('status', 'expired');
    }

    // Helper methods
    public function isActive()
    {
        return $this->status === 'active' && $this->end_date >= Carbon::now()->toDateString();
    }

    public function isExpired()
    {
        return $this->status === 'expired' || $this->end_date < Carbon::now()->toDateString();
    }

    public function getRemainingDays()
    {
        if ($this->isExpired()) {
            return 0;
        }
        
        return Carbon::now()->diffInDays(Carbon::parse($this->end_date), false);
    }

    public function canVisit()
    {
        return $this->isActive() && $this->remaining_visits > 0;
    }
}
