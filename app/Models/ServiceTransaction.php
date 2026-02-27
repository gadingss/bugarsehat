<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceTransaction extends Model
{
    use HasFactory;

    protected $table = 'additional_service_transactions';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'user_id',
        'service_id',
        'trainer_id',
        'transaction_date',
        'amount',
        'status',
        'payment_method',
        'payment_proof',
        'notes',
        'scheduled_date',
        'completed_date',
        'validated_by',
        'validated_at'
    ];

    protected $casts = [
        'transaction_date' => 'datetime',
        'scheduled_date' => 'datetime',
        'completed_date' => 'datetime',
        'amount' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function trainer()
    {
        return $this->belongsTo(User::class, 'trainer_id');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    public function scopeScheduled($query)
    {
        return $query->where('status', 'scheduled');
    }

    // Helper methods
    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isCompleted()
    {
        return $this->status === 'completed';
    }

    public function isCancelled()
    {
        return $this->status === 'cancelled';
    }

    public function isScheduled()
    {
        return $this->status === 'scheduled';
    }

    public function canCancel()
    {
        return in_array($this->status, ['pending', 'scheduled']) &&
            (!$this->scheduled_date || $this->scheduled_date > now()->addHours(2));
    }

    public function markAsCompleted()
    {
        $this->status = 'completed';
        $this->completed_date = now();
        $this->save();
    }

    public function cancel($reason = null)
    {
        if ($this->canCancel()) {
            $this->status = 'cancelled';
            if ($reason) {
                $this->notes = ($this->notes ? $this->notes . ' | ' : '') . 'Cancelled: ' . $reason;
            }
            $this->save();
            return true;
        }
        return false;
    }
}
