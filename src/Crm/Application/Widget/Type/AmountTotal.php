<?php

declare(strict_types=1);

namespace App\Crm\Application\Widget\Type;

use App\Widget\WidgetInterface;

final class AmountTotal extends AbstractAmountPeriod
{
    /**
     * @param array<string, string|bool|int|null|array<string, mixed>> $options
     @return array<string, string|bool|int|null|array<string, mixed>>
     */
    public function getOptions(array $options = []): array
    {
        return array_merge([
            'color' => WidgetInterface::COLOR_TOTAL,
        ], parent::getOptions($options));
    }

    public function getId(): string
    {
        return 'amountTotal';
    }

    public function getPermissions(): array
    {
        return ['view_all_data'];
    }

    /**
     * @param array<string, string|bool|int|null|array<string, mixed>> $options
     */
    public function getData(array $options = []): mixed
    {
        return $this->getRevenue(null, null, $options);
    }
}
