<?php

declare(strict_types=1);

namespace App\Crm\Application\Widget\Type;

use App\Crm\Domain\Repository\CustomerRepository;
use App\Crm\Domain\Repository\Query\CustomerQuery;
use App\Widget\WidgetInterface;

final class TotalsCustomer extends AbstractWidget
{
    public function __construct(
        private CustomerRepository $customer
    ) {
    }

    public function getTitle(): string
    {
        return 'stats.customerTotal';
    }

    /**
     * @param array<string, string|bool|int|null|array<string, mixed>> $options
     @return array<string, string|bool|int|null|array<string, mixed>>
     */
    public function getOptions(array $options = []): array
    {
        return array_merge([
            'route' => 'admin_customer',
            'icon' => 'customer',
            'color' => WidgetInterface::COLOR_TOTAL,
        ], parent::getOptions($options));
    }

    /**
     * @param array<string, string|bool|int|null|array<string, mixed>> $options
     */
    public function getData(array $options = []): mixed
    {
        $user = $this->getUser();
        $query = new CustomerQuery();
        $query->setCurrentUser($user);

        return $this->customer->countCustomersForQuery($query);
    }

    /**
     * @return string[]
     */
    public function getPermissions(): array
    {
        return ['view_customer', 'view_teamlead_customer', 'view_team_customer'];
    }

    public function getTemplateName(): string
    {
        return 'widget/widget-more.html.twig';
    }

    public function getId(): string
    {
        return 'TotalsCustomer';
    }
}
