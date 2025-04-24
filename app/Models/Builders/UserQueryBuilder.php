<?php

namespace App\Models\Builders;

use App\Enums\Role;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class UserQueryBuilder extends Builder
{
    /**
     * Filter users by role.
     *
     * @param Role $role
     * @return self
     */
    public function byRole(Role $role): self
    {
        return $this->where('role', $role->value);
    }

    /**
     * Get admin users.
     *
     * @return self
     */
    public function admins(): self
    {
        return $this->where('role', Role::ADMIN->value);
    }

    /**
     * Get moderator users.
     *
     * @return self
     */
    public function moderators(): self
    {
        return $this->where('role', Role::MODERATOR->value);
    }

    /**
     * Get users who allow adult content.
     *
     * @return self
     */
    public function allowedAdults(): self
    {
        return $this->where('allow_adult', true);
    }

    /**
     * Get active users based on last seen date.
     *
     * @param int $days Number of days to consider
     * @return self
     */
    public function active(int $days = 30): self
    {
        $date = Carbon::now()->subDays($days);
        return $this->where('last_seen_at', '>=', $date);
    }

    /**
     * Get inactive users based on last seen date.
     *
     * @param int $days Number of days to consider
     * @return self
     */
    public function inactive(int $days = 30): self
    {
        $date = Carbon::now()->subDays($days);
        return $this->where(function ($query) use ($date) {
            $query->where('last_seen_at', '<', $date)
                ->orWhereNull('last_seen_at');
        });
    }

    /**
     * Get users with active subscriptions.
     *
     * @return self
     */
    public function withActiveSubscriptions(): self
    {
        return $this->whereHas('subscriptions', function ($query) {
            $query->where('is_active', true);
        });
    }

    /**
     * Get users with expired subscriptions.
     *
     * @return self
     */
    public function withExpiredSubscriptions(): self
    {
        return $this->whereHas('subscriptions', function ($query) {
            $query->where('end_date', '<', now())
                ->where('is_active', false);
        });
    }

    /**
     * Get users with auto-renewable subscriptions.
     *
     * @return self
     */
    public function withAutoRenewableSubscriptions(): self
    {
        return $this->whereHas('subscriptions', function ($query) {
            $query->where('auto_renew', true);
        });
    }

    /**
     * Get users by age range.
     *
     * @param int $minAge
     * @param int $maxAge
     * @return self
     */
    public function byAgeRange(int $minAge, int $maxAge): self
    {
        $minDate = Carbon::now()->subYears($maxAge);
        $maxDate = Carbon::now()->subYears($minAge);

        return $this->whereBetween('birthday', [$minDate, $maxDate]);
    }

    /**
     * Get users with specific settings.
     *
     * @param bool $autoNext
     * @param bool $autoPlay
     * @param bool $autoSkipIntro
     * @return self
     */
    public function withSettings(bool $autoNext = null, bool $autoPlay = null, bool $autoSkipIntro = null): self
    {
        $query = $this;

        if ($autoNext !== null) {
            $query = $query->where('is_auto_next', $autoNext);
        }

        if ($autoPlay !== null) {
            $query = $query->where('is_auto_play', $autoPlay);
        }

        if ($autoSkipIntro !== null) {
            $query = $query->where('is_auto_skip_intro', $autoSkipIntro);
        }

        return $query;
    }

    /**
     * Get banned users.
     *
     * @return self
     */
    public function banned(): self
    {
        return $this->where('is_banned', true);
    }

    /**
     * Get non-banned users.
     *
     * @return self
     */
    public function notBanned(): self
    {
        return $this->where('is_banned', false);
    }
}
