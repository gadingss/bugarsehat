<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class CheckinLog extends Model
{
    use HasFactory;
    
    protected $table = 'checkin_logs';
    protected $primaryKey = 'id';
    
    // Baris "public $timestamps = false;" sudah dihapus dari sini
    
    protected $fillable = [
        'user_id',
        'staff_id',
        'membership_id',
        'checkin_time',
        'checkout_time',
        'notes'
    ];

    protected $casts = [
        'checkin_time' => 'datetime',
        'checkout_time' => 'datetime',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function staff()
    {
        return $this->belongsTo(User::class, 'staff_id');
    }

    public function membership()
    {
        return $this->belongsTo(Membership::class);
    }

    // Scopes
    public function scopeToday($query)
    {
        return $query->whereDate('checkin_time', Carbon::today());
    }

    public function scopeThisWeek($query)
    {
        return $query->whereBetween('checkin_time', [
            Carbon::now()->startOfWeek(),
            Carbon::now()->endOfWeek()
        ]);
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('checkin_time', Carbon::now()->month)
                     ->whereYear('checkin_time', Carbon::now()->year);
    }

    // Helper methods
    public function getDuration()
    {
        if (!$this->checkout_time) {
            return null;
        }
        
        return $this->checkin_time->diffInMinutes($this->checkout_time);
    }

    public function getFormattedDuration()
    {
        $duration = $this->getDuration();
        
        if (is_null($duration)) {
            return 'Belum checkout';
        }
        
        $hours = floor($duration / 60);
        $minutes = $duration % 60;
        
        if ($hours > 0 && $minutes > 0) {
            return $hours . ' jam ' . $minutes . ' menit';
        } elseif ($hours > 0) {
            return $hours . ' jam';
        } else {
            return $minutes . ' menit';
        }
    }

    public function isActive()
    {
        return is_null($this->checkout_time);
    }
}