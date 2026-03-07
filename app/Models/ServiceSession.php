<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_transaction_id',
        'session_number',
        'topic',
        'scheduled_date',
        'trainer_id',
        'status',
        'checkin_id',
    ];

    protected $casts = [
        'scheduled_date' => 'datetime',
    ];

    public function serviceTransaction(): BelongsTo
    {
        return $this->belongsTo(ServiceTransaction::class, 'service_transaction_id');
    }

    public function trainer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'trainer_id');
    }

    public function checkinLog(): BelongsTo
    {
        return $this->belongsTo(CheckinLog::class, 'checkin_id');
    }
}
