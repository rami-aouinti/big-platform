<?php

declare(strict_types=1);

namespace App\Crm\Application\Service\Timesheet;

/**
 * A static helper class for re-usable functionality.
 */
final class Util
{
    private function __construct()
    {
    }

    /**
     * Calculates the rate for a hourly rate and a given duration in seconds.
     */
    public static function calculateRate(float $hourlyRate, int $seconds): float
    {
        $rate = $hourlyRate * ($seconds / 3600);

        return round($rate, 4);
    }
}
