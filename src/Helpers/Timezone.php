<?php

namespace FoxEngineers\AdminCP\Helpers;

use Carbon\Carbon;

/**
 * Class Timezone.
 */
class Timezone
{
    /**
     * @param string $userTimezone
     * @param Carbon $date
     * @param string $format
     *
     * @return Carbon
     */
    public function convertToLocal(string $userTimezone, Carbon $date, string $format = 'Y-m-d H:i:s'): string
    {
        return $date->setTimezone($userTimezone ?? config('app.timezone'))->format($format);
    }

    /**
     * @param string $userTimezone
     * @param string $date
     *
     * @return Carbon
     */
    public function convertFromLocal(string $userTimezone, string $date): Carbon
    {
        return Carbon::parse($date, $userTimezone)->setTimezone('UTC');
    }
}