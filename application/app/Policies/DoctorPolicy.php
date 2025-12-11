<?php

namespace App\Policies;

use App\Models\Doctor;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class DoctorPolicy
{

    public function viewAny(User $user) {
        return $user->can('view doctor');
    }

    public function view(User $user, Doctor $doctor) {
        return $user->can('view doctor');
    }

    public function create(User $user) {
        return $user->can('create doctor');`
    }

    public function update(User $user, Doctor $doctor) {
        return $user->can('edit doctor');
    }

    public function delete(User $user, Doctor $doctor) {
        return $user->can('delete doctor');
    }
    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Doctor $doctor): bool
    {
        return $user->can('delete doctor');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Doctor $doctor): bool
    {
        return $user->can('delete doctor');
    }
}
