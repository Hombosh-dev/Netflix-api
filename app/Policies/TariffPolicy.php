<?php

namespace App\Policies;

use App\Models\Tariff;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TariffPolicy
{
    use HandlesAuthorization;

    public function before(User $user, $ability): ?bool
    {
        if ($user->isAdmin()) {
            return true; // Адміни можуть усе
        }
        return null;
    }

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(?User $user): bool
    {
        return true; // Anyone can view tariffs
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(?User $user, Tariff $tariff): bool
    {
        return true; // Anyone can view a tariff
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Tariff $tariff): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Tariff $tariff): bool
    {
        return false;
    }
}
