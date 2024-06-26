<?php

declare(strict_types=1);

namespace App\Crm\Application\Service\WorkingTime;

use App\Crm\Application\Service\Timesheet\DateTimeFactory;
use App\Crm\Application\Service\WorkingTime\Model\Month;
use App\Crm\Application\Service\WorkingTime\Model\Year;
use App\Crm\Application\Service\WorkingTime\Model\YearPerUserSummary;
use App\Crm\Domain\Entity\WorkingTime;
use App\Crm\Domain\Repository\TimesheetRepository;
use App\Crm\Domain\Repository\WorkingTimeRepository;
use App\Crm\Transport\Event\WorkingTimeApproveMonthEvent;
use App\Crm\Transport\Event\WorkingTimeYearEvent;
use App\Crm\Transport\Event\WorkingTimeYearSummaryEvent;
use App\User\Domain\Entity\User;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query\QueryException;
use Exception;
use Psr\EventDispatcher\EventDispatcherInterface;

use function array_key_exists;

/**
 * @internal this API and the entire namespace is instable: you should expect changes!
 */
final readonly class WorkingTimeService
{
    public function __construct(
        private TimesheetRepository $timesheetRepository,
        private WorkingTimeRepository $workingTimeRepository,
        private EventDispatcherInterface $eventDispatcher
    ) {
    }

    public function getYearSummary(Year $year, DateTimeInterface $until): YearPerUserSummary
    {
        $yearPerUserSummary = new YearPerUserSummary($year);

        $summaryEvent = new WorkingTimeYearSummaryEvent($yearPerUserSummary, $until);
        $this->eventDispatcher->dispatch($summaryEvent);

        return $yearPerUserSummary;
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function getLatestApproval(User $user): ?WorkingTime
    {
        return $this->workingTimeRepository->getLatestApproval($user);
    }

    /**
     * @throws QueryException
     * @throws Exception
     */
    public function getYear(User $user, DateTimeInterface $yearDate, DateTimeInterface $until): Year
    {
        $yearTimes = $this->workingTimeRepository->findForYear($user, $yearDate);
        $existing = [];
        foreach ($yearTimes as $workingTime) {
            $existing[$workingTime->getDate()->format('Y-m-d')] = $workingTime;
        }

        $year = new Year(DateTimeImmutable::createFromInterface($yearDate), $user);

        $stats = null;
        $firstDay = $user->getWorkStartingDay();

        foreach ($year->getMonths() as $month) {
            foreach ($month->getDays() as $day) {
                $key = $day->getDay()->format('Y-m-d');
                if (array_key_exists($key, $existing)) {
                    $day->setWorkingTime($existing[$key]);

                    continue;
                }

                if ($stats === null) {
                    $stats = $this->getYearStatistics($yearDate, $user);
                }

                $dayDate = $day->getDay();
                $result = new WorkingTime($user, $dayDate);

                if ($firstDay === null || $firstDay <= $dayDate) {
                    $result->setExpectedTime($user->getWorkHoursForDay($dayDate));
                }

                if (array_key_exists($key, $stats)) {
                    $result->setActualTime($stats[$key]);
                }

                $day->setWorkingTime($result);
            }
        }

        $event = new WorkingTimeYearEvent($year, $until);
        $this->eventDispatcher->dispatch($event);

        return $year;
    }

    /**
     * @throws QueryException
     */
    public function getMonth(User $user, DateTimeInterface $monthDate, DateTimeInterface $until): Month
    {
        // uses the year, because that triggers the required events to collect all different working times
        $year = $this->getYear($user, $monthDate, $until);

        return $year->getMonth($monthDate);
    }

    public function approveMonth(User $user, Month $month, DateTimeInterface $approvalDate, User $approvedBy): void
    {
        foreach ($month->getDays() as $day) {
            $workingTime = $day->getWorkingTime();
            if ($workingTime === null) {
                continue;
            }

            if ($workingTime->getId() !== null) {
                continue;
            }

            if ($month->isLocked() || $workingTime->isApproved()) {
                continue;
            }

            $workingTime->setApprovedBy($approvedBy);
            // FIXME see calling method
            $workingTime->setApprovedAt(DateTimeImmutable::createFromInterface($approvalDate));
            $this->workingTimeRepository->scheduleWorkingTimeUpdate($workingTime);
        }

        $this->workingTimeRepository->persistScheduledWorkingTimes();

        $this->eventDispatcher->dispatch(new WorkingTimeApproveMonthEvent($user, $month, $approvalDate, $approvedBy));
    }

    /**
     * @throws Exception
     * @return array<string, int>
     */
    private function getYearStatistics(DateTimeInterface $year, User $user): array
    {
        $dateTimeFactory = DateTimeFactory::createByUser($user);
        $begin = $dateTimeFactory->createStartOfYear($year);
        $end = $dateTimeFactory->createEndOfYear($year);

        $qb = $this->timesheetRepository->createQueryBuilder('t');

        $qb
            ->select('COALESCE(SUM(t.duration), 0) as duration')
            ->addSelect('DATE(t.date) as day')
            ->where($qb->expr()->isNotNull('t.end'))
            ->andWhere($qb->expr()->between('t.date', ':begin', ':end'))
            ->andWhere($qb->expr()->eq('t.user', ':user'))
            ->setParameter('begin', $begin->format('Y-m-d'))
            ->setParameter('end', $end->format('Y-m-d'))
            ->setParameter('user', $user->getId())
            ->addGroupBy('day')
        ;

        $results = $qb->getQuery()->getResult();

        $durations = [];
        foreach ($results as $row) {
            $durations[$row['day']] = (int)$row['duration'];
        }

        return $durations; // @phpstan-ignore-line
    }
}
