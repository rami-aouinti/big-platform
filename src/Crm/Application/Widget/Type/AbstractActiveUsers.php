<?php

declare(strict_types=1);

namespace App\Crm\Application\Widget\Type;

abstract class AbstractActiveUsers extends AbstractWidgetType
{
    /**
     * @param array<string, string|bool|int|null|array<string, mixed>> $options
     @return array<string, string|bool|int|null|array<string, mixed>>
     */
    public function getOptions(array $options = []): array
    {
        return array_merge([
            'icon' => 'users',
        ], parent::getOptions($options));
    }

    public function getTitle(): string
    {
        return 'stats.' . lcfirst($this->getId());
    }

    public function getPermissions(): array
    {
        return ['ROLE_TEAMLEAD'];
    }

    public function getTemplateName(): string
    {
        return 'widget/widget-counter.html.twig';
    }
}
