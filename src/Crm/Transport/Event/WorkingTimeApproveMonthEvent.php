<?php

declare(strict_types=1);

namespace App\Crm\Transport\Event;

use App\Crm\Application\Model\Month;
use App\User\Domain\Entity\User;
use DateTimeInterface;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * @package App\Crm\Transport\Event
 * @author  Rami Aouinti <rami.aouinti@tkdeutschland.de>
 */
final class WorkingTimeApproveMonthEvent extends Event
{
    public function __construct(
        private readonly User $user,
        private readonly Month $month,
        private readonly DateTimeInterface $approvalDate,
        private readonly User $approver
    ) {
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getMonth(): Month
    {
        return $this->month;
    }

    public function getApprovalDate(): DateTimeInterface
    {
        return $this->approvalDate;
    }

    public function getApprover(): User
    {
        return $this->approver;
    }
}
