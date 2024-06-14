<?php

declare(strict_types=1);

namespace App\Crm\Transport\API\Export\Base;

use App\Crm\Application\Model\TimesheetCountedStatistic;
use App\Crm\Application\Service\Activity\ActivityStatisticService;
use App\Crm\Application\Service\Project\ProjectStatisticService;
use App\Crm\Domain\Entity\ExportableItem;
use App\Crm\Domain\Repository\Query\TimesheetQuery;
use DateTime;
use DateTimeZone;
use Exception;

trait RendererTrait
{
    /**
     * FIXME use statistic events to calculate budgets and do NOT iterate all results!
     *
     * @param ExportableItem[] $exportItems
     */
    protected function calculateSummary(array $exportItems): array
    {
        $summary = [];

        foreach ($exportItems as $exportItem) {
            $customerId = 'none';
            $projectId = 'none';
            $activityId = 'none';
            $userId = 'none';
            $customer = null;
            $project = null;
            $activity = null;
            $currency = null;

            $project = $exportItem->getProject();
            if (null !== ($project)) {
                $customer = $project->getCustomer();
                $customerId = $customer->getId();
                $projectId = $project->getId();
                $currency = $customer->getCurrency();
            }

            $activity = $exportItem->getActivity();
            if (null !== ($activity)) {
                $activityId = $exportItem->getActivity()->getId();
            }

            $user = $exportItem->getUser();
            if (null !== ($user)) {
                $userId = $user->getId();
            }

            $id = $customerId . '_' . $projectId;
            $type = $exportItem->getType();
            $category = $exportItem->getCategory();

            if (!isset($summary[$id])) {
                $summary[$id] = [
                    'customer' => '',
                    'customer_item' => null,
                    'project' => '',
                    'project_item' => null,
                    'activities' => [],
                    'currency' => $currency,
                    'rate' => 0,
                    'rate_internal' => 0,
                    'duration' => 0,
                    'type' => [],
                    'types' => [],
                    'users' => [],
                ];

                if ($project !== null) {
                    $summary[$id]['customer'] = $customer->getName();
                    $summary[$id]['customer_item'] = $customer;
                    $summary[$id]['project'] = $project->getName();
                    $summary[$id]['project_item'] = $project;
                }
            }

            if (!isset($summary[$id]['type'][$type])) {
                $summary[$id]['type'][$type] = [
                    'rate' => 0,
                    'rate_internal' => 0,
                    'duration' => 0,
                ];
            }

            if (!isset($summary[$id]['types'][$type][$category])) {
                $summary[$id]['types'][$type][$category] = [
                    'rate' => 0,
                    'rate_internal' => 0,
                    'duration' => 0,
                ];
            }

            if (!isset($summary[$id]['activities'][$activityId])) {
                $summary[$id]['activities'][$activityId] = [
                    'activity' => '',
                    'currency' => $currency,
                    'rate' => 0,
                    'rate_internal' => 0,
                    'duration' => 0,
                    'users' => [],
                ];

                if ($activity !== null) {
                    $summary[$id]['activities'][$activityId]['activity'] = $activity->getName();
                    $summary[$id]['activities'][$activityId]['activity_item'] = $activity;
                }
            }

            if (!isset($summary[$id]['users'][$userId])) {
                $summary[$id]['users'][$userId] = [
                    'user' => $user,
                    'rate' => 0,
                    'rate_internal' => 0,
                    'duration' => 0,
                ];
            }
            if (!isset($summary[$id]['activities'][$activityId]['users'][$userId])) {
                $summary[$id]['activities'][$activityId]['users'][$userId] = [
                    'user' => $user,
                    'rate' => 0,
                    'rate_internal' => 0,
                    'duration' => 0,
                ];
            }

            $duration = $exportItem->getDuration();
            if ($duration === null) {
                $duration = 0;
            }

            $rate = $exportItem->getRate();
            $internalRate = $exportItem->getInternalRate() ?? 0;

            // rate
            $summary[$id]['rate'] += $rate;
            $summary[$id]['type'][$type]['rate'] += $rate;
            $summary[$id]['types'][$type][$category]['rate'] += $rate;
            $summary[$id]['users'][$userId]['rate'] += $rate;
            $summary[$id]['activities'][$activityId]['rate'] += $rate;
            $summary[$id]['activities'][$activityId]['users'][$userId]['rate'] += $rate;

            // internal rate
            $summary[$id]['rate_internal'] += $internalRate;
            $summary[$id]['type'][$type]['rate_internal'] += $internalRate;
            $summary[$id]['types'][$type][$category]['rate_internal'] += $internalRate;
            $summary[$id]['users'][$userId]['rate_internal'] += $internalRate;
            $summary[$id]['activities'][$activityId]['rate_internal'] += $internalRate;
            $summary[$id]['activities'][$activityId]['users'][$userId]['rate_internal'] += $internalRate;

            // duration
            $summary[$id]['duration'] += $duration;
            $summary[$id]['type'][$type]['duration'] += $duration;
            $summary[$id]['types'][$type][$category]['duration'] += $duration;
            $summary[$id]['users'][$userId]['duration'] += $duration;
            $summary[$id]['activities'][$activityId]['duration'] += $duration;
            $summary[$id]['activities'][$activityId]['users'][$userId]['duration'] += $duration;
        }

        asort($summary);

        return $summary;
    }

    /**
     * @param ExportableItem[] $exportItems
     */
    protected function calculateProjectBudget(array $exportItems, TimesheetQuery $query, ProjectStatisticService $projectStatisticService): array
    {
        $summary = [];
        $projects = [];
        $empty = new TimesheetCountedStatistic();

        foreach ($exportItems as $exportItem) {
            $customer = null;
            $project = null;
            $customerId = 'none';
            $projectId = 'none';

            $project = $exportItem->getProject();
            if (null !== ($project)) {
                $customer = $project->getCustomer();
                $customerId = $customer->getId();
                $projectId = $project->getId();
                $projects[] = $project;
            }

            $id = $customerId . '_' . $projectId;

            if (!isset($summary[$id])) {
                $summary[$id] = [
                    'totals' => $empty->jsonSerialize(),
                    'time' => $project->getTimeBudget(),
                    'money' => $project->getBudget(),
                    'time_left' => null,
                    'money_left' => null,
                    'time_left_total' => null,
                    'money_left_total' => null,
                ];
            }
        }

        $today = $this->getToday($query);

        $allBudgets = $projectStatisticService->getBudgetStatisticModelForProjects($projects, $today);

        foreach ($allBudgets as $projectId => $statisticModel) {
            $project = $statisticModel->getProject();
            $id = $project->getCustomer()->getId() . '_' . $projectId;
            $total = $statisticModel->getStatisticTotal();
            $summary[$id]['totals'] = $total->jsonSerialize();
            if ($statisticModel->hasTimeBudget()) {
                $summary[$id]['time_left'] = $statisticModel->getTimeBudgetOpenRelative();
                $summary[$id]['time_left_total'] = $statisticModel->getTimeBudgetOpen();
            }
            if ($statisticModel->hasBudget()) {
                $summary[$id]['money_left'] = $statisticModel->getBudgetOpenRelative();
                $summary[$id]['money_left_total'] = $statisticModel->getBudgetOpen();
            }
        }

        return $summary;
    }

    /**
     * @param ExportableItem[] $exportItems
     */
    protected function calculateActivityBudget(array $exportItems, TimesheetQuery $query, ActivityStatisticService $activityStatisticService): array
    {
        $summary = [];
        $activities = [];
        $empty = new TimesheetCountedStatistic();

        foreach ($exportItems as $exportItem) {
            $customerId = 'none';
            $projectId = 'none';
            $project = null;
            $activity = null;

            $activity = $exportItem->getActivity();
            if (null === ($activity)) {
                continue;
            }

            if ($activity->isGlobal()) {
                continue;
            }

            $activities[] = $activity;

            $project = $exportItem->getProject();
            if (null !== ($project)) {
                $projectId = $project->getId();
                $customerId = $project->getCustomer()->getId();
            }

            $id = $customerId . '_' . $projectId;

            if (!isset($summary[$id])) {
                $summary[$id] = [];
            }

            $activityId = $activity->getId();

            if (!isset($summary[$id][$activityId])) {
                $summary[$id][$activityId] = [
                    'totals' => $empty->jsonSerialize(),
                    'time' => $activity->getTimeBudget(),
                    'money' => $activity->getBudget(),
                    'time_left' => null,
                    'money_left' => null,
                    'time_left_total' => null,
                    'money_left_total' => null,
                ];
            }
        }

        $today = $this->getToday($query);

        $allBudgets = $activityStatisticService->getBudgetStatisticModelForActivities($activities, $today);

        foreach ($allBudgets as $activityId => $statisticModel) {
            $project = $statisticModel->getActivity()->getProject();
            $id = $project->getCustomer()->getId() . '_' . $project->getId();
            $total = $statisticModel->getStatisticTotal();
            $summary[$id][$activityId]['totals'] = $total->jsonSerialize();
            if ($statisticModel->hasTimeBudget()) {
                $summary[$id][$activityId]['time_left'] = $statisticModel->getTimeBudgetOpenRelative();
                $summary[$id][$activityId]['time_left_total'] = $statisticModel->getTimeBudgetOpen();
            }
            if ($statisticModel->hasBudget()) {
                $summary[$id][$activityId]['money_left'] = $statisticModel->getBudgetOpenRelative();
                $summary[$id][$activityId]['money_left_total'] = $statisticModel->getBudgetOpen();
            }
        }

        return $summary;
    }

    /**
     * @throws Exception
     */
    private function getToday(TimesheetQuery $query): DateTime
    {
        $end = $query->getEnd();

        if ($end !== null) {
            return $end;
        }

        if ($query->getCurrentUser() !== null) {
            $timezone = $query->getCurrentUser()->getTimezone();

            return new DateTime('now', new DateTimeZone($timezone));
        }

        if ($query->getUser() !== null) {
            $timezone = $query->getUser()->getTimezone();

            return new DateTime('now', new DateTimeZone($timezone));
        }

        return new DateTime();
    }
}