<?php

declare(strict_types=1);

namespace App\Crm\Application\Service\Invoice\Hydrator;

use App\Crm\Application\Service\Customer\CustomerStatisticService;
use App\Crm\Application\Service\Invoice\InvoiceModel;
use App\Crm\Application\Service\Invoice\InvoiceModelHydrator;

/**
 * @package App\Crm\Application\Service\Invoice\Hydrator
 * @author  Rami Aouinti <rami.aouinti@tkdeutschland.de>
 */
final class InvoiceModelCustomerHydrator implements InvoiceModelHydrator
{
    use BudgetHydratorTrait;

    public function __construct(
        private CustomerStatisticService $customerStatisticService
    ) {
    }

    public function hydrate(InvoiceModel $model): array
    {
        $customer = $model->getCustomer();

        if ($customer === null) {
            return [];
        }

        $values = [
            'customer.id' => $customer->getId(),
            'customer.address' => $customer->getAddress() ?? '',
            'customer.name' => $customer->getName() ?? '',
            'customer.contact' => $customer->getContact() ?? '',
            'customer.company' => $customer->getCompany() ?? '',
            'customer.vat' => $customer->getVatId() ?? '', // deprecated since 2.0.15
            'customer.vat_id' => $customer->getVatId() ?? '',
            'customer.number' => $customer->getNumber() ?? '',
            'customer.country' => $customer->getCountry(),
            'customer.homepage' => $customer->getHomepage() ?? '',
            'customer.comment' => $customer->getComment() ?? '',
            'customer.email' => $customer->getEmail() ?? '',
            'customer.fax' => $customer->getFax() ?? '',
            'customer.phone' => $customer->getPhone() ?? '',
            'customer.mobile' => $customer->getMobile() ?? '',
            'customer.invoice_text' => $customer->getInvoiceText() ?? '',
        ];

        /** @var \DateTime $end */
        $end = $model->getQuery()->getEnd();
        $statistic = $this->customerStatisticService->getBudgetStatisticModel($customer, $end);

        $values = array_merge($values, $this->getBudgetValues('customer.', $statistic, $model));

        foreach ($customer->getMetaFields() as $metaField) {
            $values = array_merge($values, [
                'customer.meta.' . $metaField->getName() => $metaField->getValue(),
            ]);
        }

        return $values;
    }
}
