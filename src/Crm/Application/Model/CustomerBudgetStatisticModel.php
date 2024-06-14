<?php

declare(strict_types=1);

namespace App\Crm\Application\Model;

use App\Crm\Domain\Entity\Customer;

/**
 * Object used to unify the access to budget data in charts.
 *
 * @internal do not use in plugins, no BC promise given!
 * @method Customer getEntity()
 */
class CustomerBudgetStatisticModel extends BudgetStatisticModel
{
    public function __construct(Customer $customer)
    {
        parent::__construct($customer);
    }

    public function getCustomer(): Customer
    {
        return $this->getEntity();
    }
}
