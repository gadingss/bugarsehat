<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrainingProgress extends Model
{
    use HasFactory;

    protected $table = 'training_progress';

    protected $guarded = ['id'];

    protected $casts = [
        'date' => 'date',
    ];

    public function trainer()
    {
        return $this->belongsTo(User::class, 'trainer_id');
    }

    public function member()
    {
        return $this->belongsTo(User::class, 'member_id');
    }
}
