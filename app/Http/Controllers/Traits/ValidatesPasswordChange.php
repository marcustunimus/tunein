<?php

namespace App\Http\Controllers\Traits;

use App\Models\User;
use Illuminate\Validation\ValidationException;

trait ValidatesPasswordChange
{
    protected function validatePasswordChange(User $user, array $attributes): void
    {
        if ($attributes['password_current'] != null || $attributes['password'] != null || $attributes['password_confirmation'] != null) {
            if (password_verify($attributes['password_current'], $user->password)) {
                if ($attributes['password'] == null) {
                    throw ValidationException::withMessages(['password' => 'Input a new password that you want to have your password changed to.']);
                }
            }
            else {
                throw ValidationException::withMessages(['password_current' => 'Wrong password. Input the current password for the profile.']);
            }
        }
    }
}
