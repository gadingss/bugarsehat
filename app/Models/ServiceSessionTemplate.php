<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceSessionTemplate extends Model
{
    protected $fillable = [
        'service_id',
        'session_number',
        'topic',
    ];

    /**
     * Get the service that owns the template.
     */
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class, 'service_id');
    }
}
