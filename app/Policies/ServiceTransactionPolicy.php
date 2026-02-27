<?php

namespace App\Policies;

use App\Models\ServiceTransaction;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ServiceTransactionPolicy
{
    use HandlesAuthorization;

    public function view(User $user, ServiceTransaction $serviceTransaction)
    {
        // Owner and Staff can view all
        if ($user->hasRole('User:Staff') || $user->hasRole('User:Owner') || $user->hasRole('Super:Admin')) {
            return true;
        }

        // Members can view their own transactions
        if ($user->id === $serviceTransaction->user_id) {
            return true;
        }

        // Trainers can view transactions assigned to them
        if ($user->hasRole('User:Trainer') && $user->id === $serviceTransaction->trainer_id) {
            return true;
        }

        return false;
    }

    public function update(User $user, ServiceTransaction $serviceTransaction)
    {
        // Owner and Staff can update all
        if ($user->hasRole('User:Staff') || $user->hasRole('User:Owner') || $user->hasRole('Super:Admin')) {
            return true;
        }

        // Trainers can update transactions assigned to them (e.g., mark as completed)
        if ($user->hasRole('User:Trainer') && $user->id === $serviceTransaction->trainer_id) {
            return true;
        }

        return false;
    }
}
