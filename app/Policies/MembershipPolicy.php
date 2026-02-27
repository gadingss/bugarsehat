<?php

namespace App\Policies;

use App\Models\Membership;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class MembershipPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any memberships.
     */
    public function viewAny(User $user)
    {
        return $user->hasRole(['User:Staff', 'User:Owner']);
    }

    /**
     * Determine whether the user can view the membership.
     */
    public function view(User $user, Membership $membership)
    {
        // Members can only view their own memberships
        if ($user->hasRole('User:Member')) {
            return $user->id === $membership->user_id;
        }
        
        // Staff and Owner can view any membership
        return $user->hasRole(['User:Staff', 'User:Owner']);
    }

    /**
     * Determine whether the user can create memberships.
     */
    public function create(User $user)
    {
        return $user->hasRole(['User:Staff', 'User:Owner', 'User:Member']);
    }

    /**
     * Determine whether the user can update the membership.
     */
    public function update(User $user, Membership $membership)
    {
        // Members can only update their own pending memberships
        if ($user->hasRole('User:Member')) {
            return $user->id === $membership->user_id && in_array($membership->status, ['inactive', 'pending']);
        }
        
        // Staff and Owner can update any membership
        return $user->hasRole(['User:Staff', 'User:Owner']);
    }

    /**
     * Determine whether the user can delete the membership.
     */
    public function delete(User $user, Membership $membership)
    {
        // Only Staff and Owner can delete memberships
        return $user->hasRole(['User:Staff', 'User:Owner']);
    }

    /**
     * Determine whether the user can validate payments.
     */
    public function validatePayment(User $user)
    {
        return $user->hasRole(['User:Staff', 'User:Owner']);
    }

    /**
     * Determine whether the user can activate memberships.
     */
    public function activateMembership(User $user)
    {
        return $user->hasRole(['User:Staff', 'User:Owner']);
    }

    /**
     * Determine whether the user can approve memberships.
     */
    public function approve(User $user)
    {
        return $user->hasRole(['User:Staff', 'User:Owner']);
    }

    /**
     * Determine whether the user can reject memberships.
     */
    public function reject(User $user)
    {
        return $user->hasRole(['User:Staff', 'User:Owner']);
    }
}
