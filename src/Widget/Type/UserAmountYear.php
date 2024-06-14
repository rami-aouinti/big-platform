<?php

declare(strict_types=1);

namespace App\Widget\Type;

use App\Configuration\SystemConfiguration;
use App\Crm\Application\Model\Revenue;
use App\Crm\Domain\Repository\TimesheetRepository;
use App\Crm\Transport\Event\UserRevenueStatisticEvent;
use App\Widget\WidgetException;
use App\Widget\WidgetInterface;
use Psr\EventDispatcher\EventDispatcherInterface;

final class UserAmountYear extends AbstractCounterYear
{
    public function __construct(
        private TimesheetRepository $repository,
        SystemConfiguration $systemConfiguration,
        private EventDispatcherInterface $dispatcher
    ) {
        parent::__construct($systemConfiguration);
    }

    public function getTemplateName(): string
    {
        return 'widget/widget-counter-money.html.twig';
    }

    public function getPermissions(): array
    {
        return ['view_rate_own_timesheet'];
    }

    public function getId(): string
    {
        return 'UserAmountYear';
    }

    /**
     * @param array<string, string|bool|int|null|array<string, mixed>> $options
     @return array<string, string|bool|int|null|array<string, mixed>>
     */
    public function getOptions(array $options = []): array
    {
        return array_merge([
            'icon' => 'money',
            'color' => WidgetInterface::COLOR_YEAR,
        ], parent::getOptions($options));
    }

    protected function getFinancialYearTitle(): string
    {
        return 'stats.amountFinancialYear';
    }

    /**
     * @param array<string, string|bool|int|null|array<string, mixed>> $options
     */
    protected function getYearData(\DateTimeInterface $begin, \DateTimeInterface $end, array $options = []): mixed
    {
        try {
            /** @var array<Revenue> $data */
            $data = $this->repository->getRevenue($begin, $end, $this->getUser());

            $event = new UserRevenueStatisticEvent($this->getUser(), $begin, $end);
            foreach ($data as $row) {
                $event->addRevenue($row->getCurrency(), $row->getAmount());
            }
            $this->dispatcher->dispatch($event);

            return $event->getRevenue();
        } catch (\Exception $ex) {
            throw new WidgetException(
                'Failed loading widget data: ' . $ex->getMessage()
            );
        }
    }
}
