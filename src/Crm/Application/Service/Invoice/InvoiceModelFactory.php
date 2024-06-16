<?php

declare(strict_types=1);

namespace App\Crm\Application\Service\Invoice;

use App\Crm\Application\Service\Activity\ActivityStatisticService;
use App\Crm\Application\Service\Customer\CustomerStatisticService;
use App\Crm\Application\Service\Project\ProjectStatisticService;
use App\Crm\Domain\Repository\Query\InvoiceQuery;

/**
 * @package App\Crm\Application\Service\Invoice
 * @author  Rami Aouinti <rami.aouinti@tkdeutschland.de>
 */
final class InvoiceModelFactory
{
    public function __construct(
        private CustomerStatisticService $customerStatisticService,
        private ProjectStatisticService $projectStatisticService,
        private ActivityStatisticService $activityStatisticService
    ) {
    }

    public function createModel(InvoiceFormatter $formatter, Customer $customer, InvoiceTemplate $template, InvoiceQuery $query): InvoiceModel
    {
        $model = new InvoiceModel($formatter, $this->customerStatisticService, $this->projectStatisticService, $this->activityStatisticService);

        $model->setCustomer($customer);
        $model->setTemplate($template);
        $model->setQuery($query);

        return $model;
    }
}
