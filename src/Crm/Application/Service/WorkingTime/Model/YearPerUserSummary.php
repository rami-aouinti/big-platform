<?php

declare(strict_types=1);

namespace App\Crm\Application\Service\WorkingTime\Model;

use App\User\Domain\Entity\User;
use ArrayIterator;
use Countable;
use IteratorAggregate;
use Traversable;

use function count;

/**
 * @implements IteratorAggregate<int, YearSummary>
 */
final class YearPerUserSummary implements Countable, IteratorAggregate
{
    /**
     * @var array<YearSummary>
     */
    private array $summaries = [];

    public function __construct(
        private Year $year
    ) {
    }

    public function getYear(): Year
    {
        return $this->year;
    }

    public function getUser(): User
    {
        return $this->year->getUser();
    }

    public function count(): int
    {
        return count($this->summaries);
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->summaries);
    }

    /**
     * @return YearSummary[]
     */
    public function getSummaries(): array
    {
        return $this->summaries;
    }

    public function addSummary(YearSummary $summary): void
    {
        $this->summaries[] = $summary;
    }

    public function getExpectedTime(): int
    {
        $all = 0;
        foreach ($this->getSummaries() as $month) {
            $all += $month->getExpectedTime();
        }

        return $all;
    }

    public function getActualTime(): int
    {
        $all = 0;
        foreach ($this->getSummaries() as $month) {
            $all += $month->getActualTime();
        }

        return $all;
    }
}
