<?php

declare(strict_types=1);

namespace App\Crm\Transport\EventSubscriber;

use App\Configuration\LocaleService;
use App\Constants;
use App\Crm\Domain\Entity\UserPreference;
use App\User\Domain\Entity\User;
use KevinPapst\TablerBundle\Helper\ContextHelper;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\KernelEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Prepare all theme settings for user and context.
 */
final class ThemeOptionsSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly TokenStorageInterface $storage,
        private readonly ContextHelper $helper,
        private readonly LocaleService $localeService
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::CONTROLLER => ['setThemeOptions', 100],
        ];
    }

    public function setThemeOptions(KernelEvent $event): void
    {
        // Ignore sub-requests
        if (!$event->isMainRequest()) {
            return;
        }

        $this->helper->setAssetVersion((string)Constants::VERSION_ID);

        if ($this->localeService->isRightToLeft($event->getRequest()->getLocale())) {
            $this->helper->setIsRightToLeft(true);
        }

        // ignore events like the toolbar where we do not have a token
        if ($this->storage->getToken() === null) {
            return;
        }

        $user = $this->storage->getToken()->getUser();

        if (!($user instanceof User)) {
            return;
        }

        $skin = $user->getPreferenceValue(UserPreference::SKIN);
        if ($skin === 'dark') {
            $this->helper->setIsDarkMode(true);
        }

        // do not allow boxed layout, header is not compatible and other functions need the full size as well
        $this->helper->setIsBoxedLayout(false);
        $this->helper->setIsCondensedUserMenu(false);
        $this->helper->setIsCondensedNavbar(false);
        $this->helper->setIsNavbarOverlapping(false);
        $this->helper->setIsNavbarDark(true);
        $this->helper->setIsHeaderDark($this->helper->isDarkMode());
    }
}
