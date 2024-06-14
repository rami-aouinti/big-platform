<?php

declare(strict_types=1);

namespace App\Crm\Application\Service\WorkingTime\Model;

/**
 * @package App\Crm\Application\Service\WorkingTime\Model
 * @author  Rami Aouinti <rami.aouinti@tkdeutschland.de>
 */
final class BoxConfiguration
{
    private bool $decimal = false;
    private bool $collapsed = false;

    public function setDecimal(bool $decimal): void
    {
        $this->decimal = $decimal;
    }

    public function setCollapsed(bool $collapsed): void
    {
        $this->collapsed = $collapsed;
    }

    public function isDecimal(): bool
    {
        return $this->decimal;
    }

    public function isCollapsed(): bool
    {
        return $this->collapsed;
    }
}
