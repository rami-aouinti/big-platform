<?php

declare(strict_types=1);

namespace App\Reporting;

final class Report implements ReportInterface
{
    public function __construct(
        private string $id,
        private string $route,
        private string $label,
        private string $reportIcon,
        private string $translationDomain = 'reporting'
    ) {
    }

    public function getRoute(): string
    {
        return $this->route;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function getReportIcon(): string
    {
        return $this->reportIcon;
    }

    public function getTranslationDomain(): string
    {
        return $this->translationDomain;
    }
}
