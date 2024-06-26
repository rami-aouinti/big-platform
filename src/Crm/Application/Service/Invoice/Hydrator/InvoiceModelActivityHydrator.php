<?php

declare(strict_types=1);

namespace App\Crm\Application\Service\Invoice\Hydrator;

use App\Crm\Application\Service\Activity\ActivityStatisticService;
use App\Crm\Application\Service\Invoice\InvoiceModel;
use App\Crm\Application\Service\Invoice\InvoiceModelHydrator;
use App\Crm\Domain\Entity\Activity;

final class InvoiceModelActivityHydrator implements InvoiceModelHydrator
{
    use BudgetHydratorTrait;

    public function __construct(
        private ActivityStatisticService $activityStatistic
    ) {
    }

    public function hydrate(InvoiceModel $model): array
    {
        $activities = [];

        foreach ($model->getEntries() as $entry) {
            if ($entry->getActivity() === null) {
                continue;
            }

            $key = 'A_' . $entry->getActivity()->getId();
            if (!\array_key_exists($key, $activities)) {
                $activities[$key] = $entry->getActivity();
            }
        }

        if (\count($activities) === 0) {
            return [];
        }

        $activities = array_values($activities);

        $values = [];
        $i = 0;

        foreach ($activities as $activity) {
            $prefix = '';
            if ($i > 0) {
                $prefix = $i . '.';
            }
            $values = array_merge($values, $this->getValuesFromActivity($model, $activity, $prefix));
            $i++;
        }

        return $values;
    }

    private function getValuesFromActivity(InvoiceModel $model, Activity $activity, string $prefix): array
    {
        $prefix = 'activity.' . $prefix;

        $values = [
            $prefix . 'id' => $activity->getId(),
            $prefix . 'name' => $activity->getName() ?? '',
            $prefix . 'comment' => $activity->getComment() ?? '',
            $prefix . 'number' => $activity->getNumber() ?? '',
            $prefix . 'invoice_text' => $activity->getInvoiceText() ?? '',
        ];

        if ($model->getQuery()?->getEnd() !== null) {
            $statistic = $this->activityStatistic->getBudgetStatisticModel($activity, $model->getQuery()->getEnd());

            $values = array_merge($values, $this->getBudgetValues($prefix, $statistic, $model));
        }

        foreach ($activity->getMetaFields() as $metaField) {
            $values = array_merge($values, [
                $prefix . 'meta.' . $metaField->getName() => $metaField->getValue(),
            ]);
        }

        return $values;
    }
}
