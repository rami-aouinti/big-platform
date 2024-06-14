<?php

declare(strict_types=1);

namespace App\Reporting\CustomerMonthlyProjects;

use App\Entity\Customer;
use App\Reporting\AbstractUserList;

final class CustomerMonthlyProjects extends AbstractUserList
{
    private ?Customer $customer = null;

    public function getCustomer(): ?Customer
    {
        return $this->customer;
    }

    public function setCustomer(?Customer $customer): void
    {
        $this->customer = $customer;
    }
}
