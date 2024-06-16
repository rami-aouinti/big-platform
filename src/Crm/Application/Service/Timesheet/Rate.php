<?php

declare(strict_types=1);

namespace App\Crm\Application\Service\Timesheet;

final class Rate
{
    private float $rate;
    private float $internalRate;
    private ?float $hourlyRate;
    private ?float $fixedRate;

    public function __construct(float $rate, float $internalRate, ?float $hourlyRate = null, ?float $fixedRate = null)
    {
        $this->rate = $rate;
        $this->internalRate = $internalRate;
        $this->fixedRate = $fixedRate;
        $this->hourlyRate = $hourlyRate;
    }

    public function getRate(): float
    {
        return $this->rate;
    }

    public function getInternalRate(): float
    {
        return $this->internalRate;
    }

    public function getFixedRate(): ?float
    {
        return $this->fixedRate;
    }

    public function getHourlyRate(): ?float
    {
        return $this->hourlyRate;
    }
}
