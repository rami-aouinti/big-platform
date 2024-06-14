<?php

declare(strict_types=1);

namespace App\Reporting;

interface ReportInterface
{
    public function getId(): string;

    public function getLabel(): string;

    public function getRoute(): string;
}
