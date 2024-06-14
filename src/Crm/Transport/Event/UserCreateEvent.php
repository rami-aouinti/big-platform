<?php

declare(strict_types=1);

namespace App\Crm\Transport\Event;

/**
 * Triggered for new user instances, which might or might not be saved.
 */
final class UserCreateEvent extends AbstractUserEvent
{
}
