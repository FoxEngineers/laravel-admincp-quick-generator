<?php

namespace FoxEngineers\AdminCP\Events\Frontend\Auth;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Queue\SerializesModels;

/**
 * Class UserRegistered.
 */
class UserRegistered
{
    use SerializesModels;

    public Authenticatable $user;

    /**
     * @param Authenticatable $user
     */
    public function __construct(Authenticatable $user)
    {
        $this->user = $user;
    }
}
