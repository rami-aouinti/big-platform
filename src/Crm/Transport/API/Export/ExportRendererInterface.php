<?php

declare(strict_types=1);

namespace App\Crm\Transport\API\Export;

use App\Crm\Domain\Entity\ExportableItem;
use App\Crm\Domain\Repository\Query\TimesheetQuery;
use Symfony\Component\HttpFoundation\Response;

interface ExportRendererInterface
{
    /**
     * @param ExportableItem[] $exportItems
     */
    public function render(array $exportItems, TimesheetQuery $query): Response;

    public function getId(): string;

    public function getIcon(): string;

    public function getTitle(): string;
}
