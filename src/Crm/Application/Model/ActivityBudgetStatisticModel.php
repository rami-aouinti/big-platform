<?php

declare(strict_types=1);

namespace App\Crm\Application\Model;

use App\Crm\Domain\Entity\Activity;

/**
 * Object used to unify the access to budget data in charts.
 *
 * @internal do not use in plugins, no BC promise given!
 * @method Activity getEntity()
 */
class ActivityBudgetStatisticModel extends BudgetStatisticModel
{
    public function __construct(Activity $activity)
    {
        parent::__construct($activity);
    }

    public function getActivity(): Activity
    {
        return $this->getEntity();
    }
}
