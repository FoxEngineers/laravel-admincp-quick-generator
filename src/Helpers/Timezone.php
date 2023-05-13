<?php

namespace FoxEngineers\AdminCP\Helpers;

use Carbon\Carbon;

/**
 * Class Timezone.
 */
class Timezone
{
    private string $userTimezone;

    public function __construct(string $userTimezone)
    {
        $this->userTimezone = $userTimezone;
    }

    /**
     * @param Carbon $date
     * @param string $format
     *
     * @return Carbon
     */
    public function convertToLocal(Carbon $date, string $format = 'Y-m-d H:i:s'): string
    {
        return $date->setTimezone($this->userTimezone ?? config('app.timezone'))
            ->format($format);
    }

    /**
     * @param string $date
     *
     * @return Carbon
     */
    public function convertFromLocal(string $date): Carbon
    {
        return Carbon::parse($date, $this->userTimezone)
            ->setTimezone('UTC');
    }
}