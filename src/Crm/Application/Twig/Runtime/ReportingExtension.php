<?php

declare(strict_types=1);

namespace App\Crm\Application\Twig\Runtime;

use App\Crm\Application\Reporting\ReportingService;
use App\Crm\Application\Reporting\ReportInterface;
use App\User\Domain\Entity\User;
use Twig\Extension\RuntimeExtensionInterface;

final class ReportingExtension implements RuntimeExtensionInterface
{
    public function __construct(
        private ReportingService $service
    ) {
    }

    /**
     * @return ReportInterface[]
     */
    public function getAvailableReports(User $user): array
    {
        return $this->service->getAvailableReports($user);
    }
}
