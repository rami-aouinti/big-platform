<?php

declare(strict_types=1);

namespace App\Crm\Application\Widget\Type;

use App\Crm\Domain\Repository\TimesheetRepository;
use App\Widget\WidgetException;
use App\Widget\WidgetInterface;

final class ActiveUsersWeek extends AbstractActiveUsers
{
    public function __construct(
        private TimesheetRepository $repository
    ) {
    }

    /**
     * @param array<string, string|bool|int|null|array<string, mixed>> $options
     @return array<string, string|bool|int|null|array<string, mixed>>
     */
    public function getOptions(array $options = []): array
    {
        return array_merge([
            'color' => WidgetInterface::COLOR_WEEK,
        ], parent::getOptions($options));
    }

    public function getId(): string
    {
        return 'activeUsersWeek';
    }

    /**
     * @param array<string, string|bool|int|null|array<string, mixed>> $options
     */
    public function getData(array $options = []): mixed
    {
        try {
            return $this->repository->countActiveUsers($this->createWeekStartDate(), $this->createWeekEndDate(), null);
        } catch (\Exception $ex) {
            throw new WidgetException(
                'Failed loading widget data: ' . $ex->getMessage()
            );
        }
    }
}
