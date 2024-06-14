<?php

declare(strict_types=1);

namespace App\Crm\Transport\Event;

use App\Reporting\ReportInterface;
use App\User\Domain\Entity\User;
use Symfony\Contracts\EventDispatcher\Event;

final class ReportingEvent extends Event
{
    /**
     * @var array<string, ReportInterface>
     */
    private array $reports = [];

    public function __construct(
        private User $user
    ) {
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function addReport(ReportInterface $report): self
    {
        $this->reports[$report->getId()] = $report;

        return $this;
    }

    /**
     * @return array<ReportInterface>
     */
    public function getReports(): array
    {
        return array_values($this->reports);
    }
}
