<?php

declare(strict_types=1);

namespace App\Widget\Type;

use App\Widget\WidgetInterface;

final class UserAmountMonth extends AbstractUserRevenuePeriod
{
    /**
     * @param array<string, string|bool|int|null|array<string, mixed>> $options
     @return array<string, string|bool|int|null|array<string, mixed>>
     */
    public function getOptions(array $options = []): array
    {
        return array_merge([
            'color' => WidgetInterface::COLOR_MONTH,
        ], parent::getOptions($options));
    }

    public function getId(): string
    {
        return 'UserAmountMonth';
    }

    /**
     * @param array<string, string|bool|int|null|array<string, mixed>> $options
     */
    public function getData(array $options = []): mixed
    {
        return $this->getRevenue($this->createMonthStartDate(), $this->createMonthEndDate(), $options);
    }
}
