<?php

declare(strict_types=1);

namespace App\Twig\Runtime;

use App\Crm\Application\Model\FavoriteTimesheet;
use App\Crm\Application\Service\Timesheet\FavoriteRecordService;
use App\Crm\Domain\Repository\TimesheetRepository;
use App\Entity\Timesheet;
use App\User\Domain\Entity\User;
use Twig\Extension\RuntimeExtensionInterface;

final class TimesheetExtension implements RuntimeExtensionInterface
{
    public function __construct(
        private readonly TimesheetRepository $repository,
        private readonly FavoriteRecordService $favoriteRecordService
    ) {
    }

    /**
     * @return array<Timesheet>
     */
    public function activeEntries(User $user, bool $ticktack = true): array
    {
        return $this->repository->getActiveEntries($user, $ticktack);
    }

    /**
     * @return array<FavoriteTimesheet>
     */
    public function favoriteEntries(User $user, int $limit = 5): array
    {
        return $this->favoriteRecordService->favoriteEntries($user, $limit);
    }
}
