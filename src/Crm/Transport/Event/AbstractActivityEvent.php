<?php

declare(strict_types=1);

namespace App\Crm\Transport\Event;

use App\Crm\Domain\Entity\Activity;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Base event class to used with activity manipulations.
 */
abstract class AbstractActivityEvent extends Event
{
    public function __construct(
        private Activity $activity
    ) {
    }

    public function getActivity(): Activity
    {
        return $this->activity;
    }
}
