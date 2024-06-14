<?php

declare(strict_types=1);

namespace App\Twig\Runtime;

use App\Reporting\ReportingService;
use App\Reporting\ReportInterface;
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
