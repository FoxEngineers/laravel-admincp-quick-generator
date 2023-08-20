<?php

namespace FoxEngineers\AdminCP\Observers\User;

use Illuminate\Contracts\Auth\Authenticatable;

/**
 * Class UserObserver.
 */
class UserObserver
{
    /**
     * Listen to the User created event.
     */
    public function created(Authenticatable $user): void
    {
        $this->logPasswordHistory($user);
    }

    /**
     * Listen to the User updated event.
     */
    public function updated(Authenticatable $user): void
    {
        // Only log password history on update if the password actually changed
        if ($user->isDirty('password')) {
            $this->logPasswordHistory($user);
        }
    }

    private function logPasswordHistory(Authenticatable $user): void
    {
        if (config('access.users.password_history')) {
            $user->passwordHistories()->create([
                'password' => $user->password, // Password already hashed & saved so just take from model
            ]);
        }
    }
}
