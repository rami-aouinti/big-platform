<?php

declare(strict_types=1);

namespace App\Crm\Transport\Event;

use App\Crm\Application\Service\Calendar\CalendarSource;
use App\User\Domain\Entity\User;
use Symfony\Contracts\EventDispatcher\Event;

final class CalendarSourceEvent extends Event
{
    /**
     * @var CalendarSource[]
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

    public function addSource(CalendarSource $source): void
    {
        $this->sources[] = $source;
    }

    /**
     * @return CalendarSource[]
     */
    public function getSources(): array
    {
        return $this->sources;
    }
}
