<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'address',
        'date_of_birth',
        'gender',
        'emergency_contact',
        'emergency_phone',
        'avatar',
        'role',
        'email_verified_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'date_of_birth' => 'date',
    ];

    // Relationships
    public function memberships()
    {
        return $this->hasMany(Membership::class);
    }

    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function activeMembership()
    {
        return $this->hasOne(Membership::class)->where('status', 'active');
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function serviceTransactions()
    {
        return $this->hasMany(ServiceTransaction::class);
    }

    public function checkinLogs()
    {
        return $this->hasMany(CheckinLog::class);
    }

    public function trainingProgressesAsTrainer()
    {
        return $this->hasMany(TrainingProgress::class, 'trainer_id');
    }

    public function trainingProgressesAsMember()
    {
        return $this->hasMany(TrainingProgress::class, 'member_id');
    }

    public function assignedMembers()
    {
        return $this->belongsToMany(User::class, 'trainer_members', 'trainer_id', 'member_id');
    }

    public function assignedTrainers()
    {
        return $this->belongsToMany(User::class, 'trainer_members', 'member_id', 'trainer_id');
    }

    public function availabilities()
    {
        return $this->hasMany(TrainerAvailability::class, 'trainer_id');
    }

    // Helper methods
    public function hasActiveMembership()
    {
        return $this->memberships()->where('status', 'active')->exists();
    }

    public function getActiveMembership()
    {
        return $this->memberships()->where('status', 'active')->with('package')->first();
    }

    public function getTotalVisits()
    {
        return $this->checkinLogs()->count();
    }

    public function getThisMonthVisits()
    {
        return $this->checkinLogs()->whereMonth('checkin_time', now()->month)->count();
    }
    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [
            'email' => $this->email,
            'name' => $this->name
        ];
    }
}
