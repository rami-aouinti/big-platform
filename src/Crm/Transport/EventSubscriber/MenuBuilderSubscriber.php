<?php

declare(strict_types=1);

namespace App\Crm\Transport\EventSubscriber;

use App\Crm\Application\Utils\MenuItemModel;
use App\Crm\Application\Utils\MenuService;
use KevinPapst\TablerBundle\Event\MenuEvent;
use KevinPapst\TablerBundle\Model\MenuItemInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class MenuBuilder configures the main navigation.
 * @internal
 */
final class MenuBuilderSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private MenuService $menuService
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            MenuEvent::class => ['onSetupNavbar', 100],
        ];
    }

    public function onSetupNavbar(MenuEvent $event): void
    {
        $menuEvent = $this->menuService->getKimaiMenu();

        foreach ($menuEvent->getMenu()->getChildren() as $child) {
            if ($child->getRoute() === null && !$child->hasChildren()) {
                continue;
            }
            $event->addItem($child);
        }

        if ($menuEvent->getAppsMenu()->hasChildren()) {
            $event->addItem($menuEvent->getAppsMenu());
        }
        if ($menuEvent->getAdminMenu()->hasChildren()) {
            $event->addItem($menuEvent->getAdminMenu());
        }
        if ($menuEvent->getSystemMenu()->hasChildren()) {
            $event->addItem($menuEvent->getSystemMenu());
        }

        $route = $event->getRequest()->get('_route');
        if (!\is_string($route)) {
            return;
        }

        $this->activateByRoute($route, $event->getItems());
    }

    /**
     * @param MenuItemInterface[] $items
     */
    private function activateByRoute(string $route, array $items): bool
    {
        foreach ($items as $item) {
            if ($item instanceof MenuItemModel) {
                if ($item->isChildRoute($route)) {
                    $item->setIsActive(true);

                    return true;
                }
            }

            if ($item->getRoute() === $route) {
                $item->setIsActive(true);

                return true;
            }

            if ($item->hasChildren()) {
                if ($this->activateByRoute($route, $item->getChildren())) {
                    return true;
                }
            }
        }

        return false;
    }
}
