<?php

declare(strict_types=1);

namespace App\Crm\Transport\EventSubscriber;

use App\Crm\Application\Twig\LocaleFormatExtensions;
use App\User\Domain\Entity\User;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

final class UserEnvironmentSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly TokenStorageInterface $tokenStorage,
        private readonly AuthorizationCheckerInterface $auth,
        private readonly LocaleFormatExtensions $localeFormatExtensions
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => ['prepareEnvironment', -100],
        ];
    }

    public function prepareEnvironment(RequestEvent $event): void
    {
        // ignore sub-requests
        if (!$event->isMainRequest()) {
            return;
        }

        $locale = $event->getRequest()->getLocale();

        // events like the toolbar might not have a token
        $token = $this->tokenStorage->getToken();
        if (null !== ($token)) {
            $user = $token->getUser();

            if ($user instanceof User) {
                $locale = $user->getLocale();
                date_default_timezone_set($user->getTimezone());
                $user->initCanSeeAllData($this->auth->isGranted('view_all_data'));
            }
        }

        // the locale is primarily used for formatting values, so we depend on the user locale if available
        if (is_string($locale)) {
            \Locale::setDefault($locale);
            $this->localeFormatExtensions->setLocale($locale);
        } else {
            \Locale::setDefault($locale->value);
            $this->localeFormatExtensions->setLocale($locale->value);
        }
    }
}
