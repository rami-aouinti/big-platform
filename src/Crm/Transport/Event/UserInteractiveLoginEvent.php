<?php

declare(strict_types=1);

namespace App\Crm\Transport\Event;

/**
 * Triggered for programmatic logins (like password reset or registration).
 */
final class UserInteractiveLoginEvent extends AbstractUserEvent
{
}
