<?php

declare(strict_types=1);

namespace App\Crm\Application\Widget\Type;

use App\Crm\Domain\Repository\TimesheetRepository;
use App\Crm\Transport\Event\UserRevenueStatisticEvent;
use Psr\EventDispatcher\EventDispatcherInterface;

abstract class AbstractUserRevenuePeriod extends AbstractWidget
{
    public function __construct(
        private TimesheetRepository $repository,
        private EventDispatcherInterface $dispatcher
    ) {
    }

    public function getTitle(): string
    {
        return 'stats.' . lcfirst($this->getId());
    }

    public function getTemplateName(): string
    {
        return 'widget/widget-counter-money.html.twig';
    }

    public function getPermissions(): array
    {
        return ['view_rate_own_timesheet'];
    }

    /**
     * @param array<string, string|bool|int|null|array<string, mixed>> $options
     @return array<string, string|bool|int|null|array<string, mixed>>
     */
    public function getOptions(array $options = []): array
    {
        return array_merge([
            'icon' => 'money',
        ], parent::getOptions($options));
    }

    /**
     * @param array<string, string|bool|int|null|array<string, mixed>> $options
     * @return array<string, float>
     */
    protected function getRevenue(?\DateTimeInterface $begin, ?\DateTimeInterface $end, array $options = []): array
    {
        $user = $this->getUser();

        $data = $this->repository->getRevenue($begin, $end, $user);

        $event = new UserRevenueStatisticEvent($user, $begin, $end);
        foreach ($data as $row) {
            $event->addRevenue($row->getCurrency(), $row->getAmount());
        }
        $this->dispatcher->dispatch($event);

        return $event->getRevenue();
    }
}
