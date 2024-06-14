<?php

declare(strict_types=1);

namespace App\Crm\Transport\Event;

use App\Crm\Domain\Entity\UserPreference;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Dynamically find possible user preferences to display.
 */
final class UserPreferenceDisplayEvent extends Event
{
    public const EXPORT = 'export';
    public const USERS = 'users';

    /**
     * @var UserPreference[]
     */
    private array $preferences = [];

    public function __construct(
        private string $location
    ) {
    }

    /**
     * @return UserPreference[]
     */
    public function getPreferences(): array
    {
        return $this->preferences;
    }

    public function addPreference(UserPreference $preference): void
    {
        $this->preferences[] = $preference;
    }

    /**
     * If you want to filter where the preference will be displayed, check the current location.
     */
    public function getLocation(): string
    {
        return $this->location;
    }
}
