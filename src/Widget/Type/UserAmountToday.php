<?php

declare(strict_types=1);

namespace App\Widget\Type;

use App\Widget\WidgetInterface;

final class UserAmountToday extends AbstractUserRevenuePeriod
{
    /**
     * @param array<string, string|bool|int|null|array<string, mixed>> $options
     @return array<string, string|bool|int|null|array<string, mixed>>
     */
    public function getOptions(array $options = []): array
    {
        return array_merge([
            'color' => WidgetInterface::COLOR_TODAY,
        ], parent::getOptions($options));
    }

    public function getId(): string
    {
        return 'UserAmountToday';
    }

    /**
     * @param array<string, string|bool|int|null|array<string, mixed>> $options
     */
    public function getData(array $options = []): mixed
    {
        return $this->getRevenue($this->createTodayStartDate(), $this->createTodayEndDate(), $options);
    }
}
