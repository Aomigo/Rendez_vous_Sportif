<?php

namespace App\Policies;

use App\Models\Event;
use App\Models\Organization;
use App\Models\User;

class EventPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Event $event): bool
    {
        return (bool) $user->is_admin
            || ($event->organization !== null && $event->organization->hasMember($user));
    }

    /**
     * Determine whether the user can see the organization's events dashboard.
     */
    public function viewDashboard(User $user, Organization $organization): bool
    {
        return (bool) $user->is_admin || $organization->isOwnedBy($user);
    }

    /**
     * Determine whether the user can manage the event and see its participants.
     */
    public function manage(User $user, Event $event): bool
    {
        return $this->update($user, $event);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user, ?Organization $organization = null): bool
    {
        $organization ??= $user->currentOrganization();

        if ($organization === null) {
            return false;
        }

        return (bool) $user->is_admin || $organization->isOwnedBy($user);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Event $event): bool
    {
        return (bool) $user->is_admin
            || ($event->organization !== null && $event->organization->isOwnedBy($user));
    }

    /**
     * Determine whether the user can cancel the model.
     */
    public function cancel(User $user, Event $event): bool
    {
        return (bool) $user->is_admin
            || ($event->organization !== null && $event->organization->isOwnedBy($user));
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Event $event): bool
    {
        return $this->cancel($user, $event);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Event $event): bool
    {
        return (bool) $user->is_admin;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Event $event): bool
    {
        return (bool) $user->is_admin;
    }
}
