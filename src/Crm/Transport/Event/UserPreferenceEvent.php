<?php

declare(strict_types=1);

namespace App\Crm\Transport\Event;

use App\Crm\Domain\Entity\UserPreference;
use App\User\Domain\Entity\User;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Add further user-preference definitions dynamically.
 * This is used on every page load, do not query the database when this is dispatched.
 */
final class UserPreferenceEvent extends Event
{
    /**
     * @param array<UserPreference> $preferences
     */
    public function __construct(
        private readonly User $user,
        private array $preferences,
        private readonly bool $booting = true
    ) {
    }

    /**
     * Whether this event is dispatched for the currently logged in user during kernel boot.
     */
    public function isBooting(): bool
    {
        return $this->booting;
    }

    /**
     * Do not set the preferences directly to the user object, but ONLY via addPreference()
     */
    public function getUser(): User
    {
        return $this->user;
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
        foreach ($this->preferences as $pref) {
            if (strtolower($pref->getName()) === strtolower($preference->getName())) {
                throw new \InvalidArgumentException(
                    'Cannot add user preference, one with the name "' . $preference->getName() . '" is already existing'
                );
            }
        }
        $this->preferences[] = $preference;
    }
}
