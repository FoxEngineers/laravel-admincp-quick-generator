<?php

namespace FoxEngineers\AdminCP\Listeners\Backend\Auth\User;

use FoxEngineers\AdminCP\Events\Backend\Auth\User\UserConfirmed;
use FoxEngineers\AdminCP\Events\Backend\Auth\User\UserCreated;
use FoxEngineers\AdminCP\Events\Backend\Auth\User\UserDeactivated;
use FoxEngineers\AdminCP\Events\Backend\Auth\User\UserDeleted;
use FoxEngineers\AdminCP\Events\Backend\Auth\User\UserPasswordChanged;
use FoxEngineers\AdminCP\Events\Backend\Auth\User\UserPermanentlyDeleted;
use FoxEngineers\AdminCP\Events\Backend\Auth\User\UserReactivated;
use FoxEngineers\AdminCP\Events\Backend\Auth\User\UserRestored;
use FoxEngineers\AdminCP\Events\Backend\Auth\User\UserSocialDeleted;
use FoxEngineers\AdminCP\Events\Backend\Auth\User\UserUnconfirmed;
use FoxEngineers\AdminCP\Events\Backend\Auth\User\UserUpdated;
use Illuminate\Support\Facades\Log;

/**
 * Class UserEventListener.
 */
class UserEventListener
{
    /**
     * @param $event
     */
    public function onCreated($event): void
    {
        Log::info('User Created');
    }

    /**
     * @param $event
     */
    public function onUpdated($event): void
    {
        Log::info('User Updated');
    }

    /**
     * @param $event
     */
    public function onDeleted($event): void
    {
        Log::info('User Deleted');
    }

    /**
     * @param $event
     */
    public function onConfirmed($event): void
    {
        Log::info('User Confirmed');
    }

    /**
     * @param $event
     */
    public function onUnconfirmed($event): void
    {
        Log::info('User Unconfirmed');
    }

    /**
     * @param $event
     */
    public function onPasswordChanged($event): void
    {
        Log::info('User Password Changed');
    }

    /**
     * @param $event
     */
    public function onDeactivated($event): void
    {
        Log::info('User Deactivated');
    }

    /**
     * @param $event
     */
    public function onReactivated($event): void
    {
        Log::info('User Reactivated');
    }

    /**
     * @param $event
     */
    public function onSocialDeleted($event): void
    {
        Log::info('User Social Deleted');
    }

    /**
     * @param $event
     */
    public function onPermanentlyDeleted($event): void
    {
        Log::info('User Permanently Deleted');
    }

    /**
     * @param $event
     */
    public function onRestored($event): void
    {
        Log::info('User Restored');
    }

    /**
     * Register the listeners for the subscriber.
     *
     * @param \Illuminate\Events\Dispatcher $events
     */
    public function subscribe($events)
    {
        $events->listen(
            UserCreated::class,
            'FoxEngineers\AdminCP\Listeners\Backend\Auth\User\UserEventListener@onCreated'
        );

        $events->listen(
            UserUpdated::class,
            'FoxEngineers\AdminCP\Listeners\Backend\Auth\User\UserEventListener@onUpdated'
        );

        $events->listen(
            UserDeleted::class,
            'FoxEngineers\AdminCP\Listeners\Backend\Auth\User\UserEventListener@onDeleted'
        );

        $events->listen(
            UserConfirmed::class,
            'FoxEngineers\AdminCP\Listeners\Backend\Auth\User\UserEventListener@onConfirmed'
        );

        $events->listen(
            UserUnconfirmed::class,
            'FoxEngineers\AdminCP\Listeners\Backend\Auth\User\UserEventListener@onUnconfirmed'
        );

        $events->listen(
            UserPasswordChanged::class,
            'FoxEngineers\AdminCP\Listeners\Backend\Auth\User\UserEventListener@onPasswordChanged'
        );

        $events->listen(
            UserDeactivated::class,
            'FoxEngineers\AdminCP\Listeners\Backend\Auth\User\UserEventListener@onDeactivated'
        );

        $events->listen(
            UserReactivated::class,
            'FoxEngineers\AdminCP\Listeners\Backend\Auth\User\UserEventListener@onReactivated'
        );

        $events->listen(
            UserSocialDeleted::class,
            'FoxEngineers\AdminCP\Listeners\Backend\Auth\User\UserEventListener@onSocialDeleted'
        );

        $events->listen(
            UserPermanentlyDeleted::class,
            'FoxEngineers\AdminCP\Listeners\Backend\Auth\User\UserEventListener@onPermanentlyDeleted'
        );

        $events->listen(
            UserRestored::class,
            'FoxEngineers\AdminCP\Listeners\Backend\Auth\User\UserEventListener@onRestored'
        );
    }
}
