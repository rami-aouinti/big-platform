<?php

declare(strict_types=1);

namespace App\Crm\Application\Widget\Type;

use App\Configuration\SystemConfiguration;
use App\Crm\Domain\Repository\TimesheetRepository;
use App\Widget\WidgetException;
use App\Widget\WidgetInterface;

final class UserDurationYear extends AbstractCounterYear
{
    public function __construct(
        private TimesheetRepository $repository,
        SystemConfiguration $systemConfiguration
    ) {
        parent::__construct($systemConfiguration);
    }

    public function getId(): string
    {
        return 'userDurationYear';
    }

    public function getTemplateName(): string
    {
        return 'widget/widget-counter-duration.html.twig';
    }

    /**
     * @param array<string, string|bool|int|null|array<string, mixed>> $options
     @return array<string, string|bool|int|null|array<string, mixed>>
     */
    public function getOptions(array $options = []): array
    {
        return array_merge([
            'icon' => 'duration',
            'color' => WidgetInterface::COLOR_YEAR,
        ], parent::getOptions($options));
    }

    /**
     * @param array<string, string|bool|int|null|array<string, mixed>> $options
     */
    protected function getYearData(\DateTimeInterface $begin, \DateTimeInterface $end, array $options = []): mixed
    {
        try {
            return $this->repository->getDurationForTimeRange($begin, $end, $this->getUser());
        } catch (\Exception $ex) {
            throw new WidgetException(
                'Failed loading widget data: ' . $ex->getMessage()
            );
        }
    }

    protected function getFinancialYearTitle(): string
    {
        return 'stats.durationFinancialYear';
    }
}
