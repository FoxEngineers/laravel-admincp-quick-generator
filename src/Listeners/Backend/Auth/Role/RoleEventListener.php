<?php

namespace FoxEngineers\AdminCP\Listeners\Backend\Auth\Role;

use FoxEngineers\AdminCP\Events\Backend\Auth\Role\RoleCreated;
use FoxEngineers\AdminCP\Events\Backend\Auth\Role\RoleDeleted;
use FoxEngineers\AdminCP\Events\Backend\Auth\Role\RoleUpdated;
use Illuminate\Support\Facades\Log;

/**
 * Class RoleEventListener.
 */
class RoleEventListener
{
    /**
     * @param $event
     */
    public function onCreated($event): void
    {
        Log::info('Role Created');
    }

    /**
     * @param $event
     */
    public function onUpdated($event): void
    {
        Log::info('Role Updated');
    }

    /**
     * @param $event
     */
    public function onDeleted($event): void
    {
        Log::info('Role Deleted');
    }

    /**
     * Register the listeners for the subscriber.
     *
     * @param \Illuminate\Events\Dispatcher $events
     */
    public function subscribe($events)
    {
        $events->listen(
            RoleCreated::class,
            'FoxEngineers\AdminCP\Listeners\Backend\Auth\Role\RoleEventListener@onCreated'
        );

        $events->listen(
            RoleUpdated::class,
            'FoxEngineers\AdminCP\Listeners\Backend\Auth\Role\RoleEventListener@onUpdated'
        );

        $events->listen(
            RoleDeleted::class,
            'FoxEngineers\AdminCP\Listeners\Backend\Auth\Role\RoleEventListener@onDeleted'
        );
    }
}
