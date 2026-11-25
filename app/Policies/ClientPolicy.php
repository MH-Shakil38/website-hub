<?php

namespace App\Policies;

use App\Models\User;

class ClientPolicy
{
    public function manageAccounts(User $user): bool
    {
        return $user->isSuperAdmin() || $user->isReseller();
    }

    public function impersonate(User $actor, User $target): bool
    {
        if ((int) $actor->id === (int) $target->id) {
            return false;
        }

        if ($actor->isSuperAdmin()) {
            return true;
        }

        if ($actor->isReseller()) {
            return $target->isClient() && (int) $target->reseller_id === (int) $actor->id;
        }

        return false;
    }
}
