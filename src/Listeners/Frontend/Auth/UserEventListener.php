<?php

namespace FoxEngineers\AdminCP\Listeners\Frontend\Auth;

use Carbon\Carbon;
use FoxEngineers\AdminCP\Events\Frontend\Auth\UserConfirmed;
use FoxEngineers\AdminCP\Events\Frontend\Auth\UserLoggedIn;
use FoxEngineers\AdminCP\Events\Frontend\Auth\UserLoggedOut;
use FoxEngineers\AdminCP\Events\Frontend\Auth\UserProviderRegistered;
use FoxEngineers\AdminCP\Events\Frontend\Auth\UserRegistered;
use Illuminate\Events\Dispatcher;
use Illuminate\Support\Facades\Log;

/**
 * Class UserEventListener.
 */
class UserEventListener
{
    public function onLoggedIn($event): void
    {
        $ip_address = request()->getClientIp();

        // Update the logging in users time & IP
        $event->user->fill([
            'last_login_at' => Carbon::now()->toDateTimeString(),
            'last_login_ip' => $ip_address,
        ]);

        // Update the timezone via IP address
        $geoip = geoip($ip_address);

        if ($event->user->timezone !== $geoip['timezone']) {
            // Update the users timezone
            $event->user->fill([
                'timezone' => $geoip['timezone'],
            ]);
        }

        $event->user->save();

        Log::info('User Logged In: '.$event->user->full_name);
    }

    public function onLoggedOut($event): void
    {
        Log::info('User Logged Out: '.$event->user->full_name);
    }

    public function onRegistered($event): void
    {
        Log::info('User Registered: '.$event->user->full_name);
    }

    public function onProviderRegistered($event): void
    {
        Log::info('User Provider Registered: '.$event->user->full_name);
    }

    public function onConfirmed($event): void
    {
        Log::info('User Confirmed: '.$event->user->full_name);
    }

    /**
     * Register the listeners for the subscriber.
     *
     * @param Dispatcher $events
     */
    public function subscribe($events): void
    {
        $events->listen(
            UserLoggedIn::class,
            'FoxEngineers\AdminCP\Listeners\Frontend\Auth\UserEventListener@onLoggedIn'
        );

        $events->listen(
            UserLoggedOut::class,
            'FoxEngineers\AdminCP\Listeners\Frontend\Auth\UserEventListener@onLoggedOut'
        );

        $events->listen(
            UserRegistered::class,
            'FoxEngineers\AdminCP\Listeners\Frontend\Auth\UserEventListener@onRegistered'
        );

        $events->listen(
            UserProviderRegistered::class,
            'FoxEngineers\AdminCP\Listeners\Frontend\Auth\UserEventListener@onProviderRegistered'
        );

        $events->listen(
            UserConfirmed::class,
            'FoxEngineers\AdminCP\Listeners\Frontend\Auth\UserEventListener@onConfirmed'
        );
    }
}
