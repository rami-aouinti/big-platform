<?php

declare(strict_types=1);

namespace App\Crm\Transport\Event;

use Symfony\Contracts\EventDispatcher\Event;

/**
 * Used to display the full revenue information for a certain date-range.
 */
final class RevenueStatisticEvent extends Event
{
    /**
     * @var array<string, float>
     */
    private array $revenue = [];

    public function __construct(
        private ?\DateTimeInterface $begin,
        private ?\DateTimeInterface $end
    ) {
    }

    public function getBegin(): ?\DateTimeInterface
    {
        return $this->begin;
    }

    public function getEnd(): ?\DateTimeInterface
    {
        return $this->end;
    }

    public function getRevenue(): array
    {
        return $this->revenue;
    }

    public function addRevenue(string $currency, float $revenue): void
    {
        if (!\array_key_exists($currency, $this->revenue)) {
            $this->revenue[$currency] = 0.0;
        }

        $this->revenue[$currency] += $revenue;
    }
}
