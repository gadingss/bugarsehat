<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Membership extends Model
{
    use HasFactory;

    protected static function booted()
    {
        static::updated(function ($membership) {
            if ($membership->isDirty('status') && $membership->status === 'active') {
                $membership->grantPackageQuotas();
            }
        });
        static::created(function ($membership) {
            if ($membership->status === 'active') {
                $membership->grantPackageQuotas();
            }
        });
    }
    
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

    public function grantPackageQuotas()
    {
        if (!$this->package) {
            return;
        }

        $alreadyGranted = \App\Models\ServiceTransaction::where('user_id', $this->user_id)
            ->where('notes', 'like', '%[Membership #'.$this->id.']%')
            ->exists();

        if ($alreadyGranted) {
            return;
        }

        foreach ($this->package->services as $service) {
            $transaction = \App\Models\ServiceTransaction::create([
                'user_id' => $this->user_id,
                'service_id' => $service->id,
                'transaction_date' => now(),
                'amount' => 0,
                'status' => 'scheduled',
                'notes' => 'Didapatkan dari Paket Membership ' . $this->package->name . ' [Membership #'.$this->id.']'
            ]);

            // Create pending sessions based on the service's sessions_count
            $sessionsCount = $service->sessions_count ?? 1;
            for ($i = 1; $i <= $sessionsCount; $i++) {
                \App\Models\ServiceSession::create([
                    'service_transaction_id' => $transaction->id,
                    'session_number' => $i,
                    'status' => 'pending',
                    // topic bisa diisi ketika booking schedule
                ]);
            }
        }
    }
}
