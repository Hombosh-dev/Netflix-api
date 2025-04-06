<?php

namespace App\Policies;

use App\Models\CommentReport;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CommentReportPolicy
{
    /**
     * Determine whether the user can view any comment reports.
     */
    public function viewAny(User $user): bool
    {
        // Only admins can view any comment reports
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can view the comment report.
     */
    public function view(User $user, CommentReport $commentReport): bool
    {
        // Allow viewing if the user is an admin or the one who created the report
        return $user->hasRole('admin') || $user->id === $commentReport->user_id;
    }

    /**
     * Determine whether the user can create a comment report.
     */
    public function create(User $user): bool
    {
        // Any authenticated user can create a report
        return $user !== null;
    }

    /**
     * Determine whether the user can update the comment report.
     */
    public function update(User $user, CommentReport $commentReport): bool
    {
        // Typically, reports should not be updated by their creators,
        // so allow only admins to update
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can delete the comment report.
     */
    public function delete(User $user, CommentReport $commentReport): bool
    {
        // Allow deletion if the user is an admin or the owner of the report
        return $user->hasRole('admin') || $user->id === $commentReport->user_id;
    }

    /**
     * Determine whether the user can restore the comment report.
     */
    public function restore(User $user, CommentReport $commentReport): bool
    {
        // Allow restore only for admins or the owner of the report
        return $user->hasRole('admin') || $user->id === $commentReport->user_id;
    }

    /**
     * Determine whether the user can permanently delete the comment report.
     */
    public function forceDelete(User $user, CommentReport $commentReport): bool
    {
        // Only admins may permanently delete reports
        return $user->hasRole('admin');
    }
}
