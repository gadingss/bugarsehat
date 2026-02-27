<?php

namespace App\Policies;

use App\Models\CheckinLog;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CheckinLogPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole(['User:Staff', 'User:Owner']);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, CheckinLog $checkinLog): bool
    {
        return $user->hasRole(['User:Staff', 'User:Owner']);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasRole(['User:Staff', 'User:Owner']);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, CheckinLog $checkinLog): bool
    {
        return $user->hasRole(['User:Staff', 'User:Owner']);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, CheckinLog $checkinLog): bool
    {
        return $user->hasRole(['User:Staff', 'User:Owner']);
    }

    /**
     * Determine whether the user can export reports.
     */
    public function export(User $user): bool
    {
        return $user->hasRole(['User:Staff', 'User:Owner']);
    }
}
