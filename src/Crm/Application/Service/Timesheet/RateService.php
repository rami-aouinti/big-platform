<?php

declare(strict_types=1);

namespace App\Crm\Application\Service\Timesheet;

use App\Crm\Domain\Entity\RateInterface;
use App\Crm\Domain\Entity\Timesheet;
use App\Crm\Domain\Entity\UserPreference;
use App\Crm\Domain\Repository\TimesheetRepository;

/**
 * Implementation to calculate the rate for a timesheet record.
 */
final readonly class RateService implements RateServiceInterface
{
    public function __construct(
        private array $rates,
        private TimesheetRepository $repository
    ) {
    }

    public function calculate(Timesheet $record): Rate
    {
        if ($record->isRunning()) {
            return new Rate(0.00, 0.00);
        }

        $fixedRate = $record->getFixedRate();
        $hourlyRate = $record->getHourlyRate();
        $fixedInternalRate = null;
        $internalRate = null;

        $rate = $this->getBestFittingRate($record);

        if ($rate !== null) {
            if ($rate->isFixed()) {
                $fixedRate ??= $rate->getRate();
                if ($rate->getInternalRate() !== null) {
                    $fixedInternalRate = $rate->getInternalRate();
                }
            } else {
                $hourlyRate ??= $rate->getRate();
                if ($rate->getInternalRate() !== null) {
                    $internalRate = $rate->getInternalRate();
                }
            }
        }

        if ($fixedRate !== null) {
            if ($fixedInternalRate === null) {
                $fixedInternalRate = (float)$record->getUser()->getPreferenceValue(UserPreference::INTERNAL_RATE, $fixedRate, false);
            }

            return new Rate($fixedRate, $fixedInternalRate, null, $fixedRate);
        }

        // user preferences => fallback if nothing else was configured
        if ($hourlyRate === null) {
            $hourlyRate = (float)$record->getUser()->getPreferenceValue(UserPreference::HOURLY_RATE, 0.00, false);
        }

        if ($internalRate === null) {
            $internalRate = (float)$record->getUser()->getPreferenceValue(UserPreference::INTERNAL_RATE, $hourlyRate, false);
        }

        $factor = 1.00;
        // do not apply once a value was calculated - see https://github.com/kimai/kimai/issues/1988
        if ($record->getFixedRate() === null && $record->getHourlyRate() === null) {
            $factor = $this->getRateFactor($record);
        }

        $factoredHourlyRate = $hourlyRate * $factor;
        $factoredInternalRate = $internalRate * $factor;
        $totalRate = 0;
        $totalInternalRate = 0;

        if ($record->getDuration() !== null) {
            $totalRate = Util::calculateRate($factoredHourlyRate, $record->getDuration());
            $totalInternalRate = Util::calculateRate($factoredInternalRate, $record->getDuration());
        }

        return new Rate($totalRate, $totalInternalRate, $factoredHourlyRate, null);
    }

    private function getBestFittingRate(Timesheet $timesheet): ?RateInterface
    {
        $rates = $this->repository->findMatchingRates($timesheet);
        /** @var RateInterface[] $sorted */
        $sorted = [];
        foreach ($rates as $rate) {
            $score = $rate->getScore();
            if ($rate->getUser() !== null && $timesheet->getUser() === $rate->getUser()) {
                $score++;
            }

            $sorted[$score] = $rate;
        }

        if (!empty($sorted)) {
            ksort($sorted);

            return end($sorted);
        }

        return null;
    }

    private function getRateFactor(Timesheet $record): float
    {
        $factor = 0.00;

        foreach ($this->rates as $rateFactor) {
            $weekday = $record->getEnd()->format('l');
            $days = array_map('strtolower', $rateFactor['days']);
            if (\in_array(strtolower($weekday), $days)) {
                $factor += (float)$rateFactor['factor'];
            }
        }

        if ($factor <= 0) {
            $factor = 1.00;
        }

        return $factor;
    }
}
