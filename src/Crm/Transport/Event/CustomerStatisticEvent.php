<?php

declare(strict_types=1);

namespace App\Crm\Transport\Event;

use App\Crm\Application\Model\CustomerStatistic;
use App\Crm\Domain\Entity\Customer;

final class CustomerStatisticEvent extends AbstractCustomerEvent
{
    public function __construct(
        Customer $customer,
        private CustomerStatistic $statistic,
        private ?\DateTime $begin = null,
        private ?\DateTime $end = null
    ) {
        parent::__construct($customer);
    }

    public function getStatistic(): CustomerStatistic
    {
        return $this->statistic;
    }

    public function getBegin(): ?\DateTime
    {
        return $this->begin;
    }

    public function getEnd(): ?\DateTime
    {
        return $this->end;
    }
}
