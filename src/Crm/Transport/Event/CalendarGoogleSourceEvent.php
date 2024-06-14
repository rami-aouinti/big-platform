<?php

declare(strict_types=1);

namespace App\Crm\Transport\Event;

use App\Crm\Application\Service\Calendar\GoogleSource;
use App\User\Domain\Entity\User;
use Symfony\Contracts\EventDispatcher\Event;

final class CalendarGoogleSourceEvent extends Event
{
    /**
     * @var GoogleSource[]
     */
    private array $sources = [];

    public function __construct(
        private User $user
    ) {
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function addSource(GoogleSource $source): self
    {
        $this->sources[] = $source;

        return $this;
    }

    /**
     * @return GoogleSource[]
     */
    public function getSources(): array
    {
        return $this->sources;
    }
}
