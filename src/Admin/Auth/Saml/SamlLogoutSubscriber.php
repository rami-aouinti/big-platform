<?php

declare(strict_types=1);

namespace App\Admin\Auth\Saml;

use OneLogin\Saml2\Error;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Http\Event\LogoutEvent;

final class SamlLogoutSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly SamlAuthFactory $samlAuth
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            LogoutEvent::class => 'logout',
        ];
    }

    public function logout(LogoutEvent $event): void
    {
        $token = $event->getToken();

        if (!$token instanceof SamlToken) {
            return;
        }

        $samlAuth = $this->samlAuth->create();

        try {
            $samlAuth->processSLO();
        } catch (Error $e) {
            if (!empty($samlAuth->getSLOurl())) {
                $sessionIndex = $token->hasAttribute('sessionIndex') ? $token->getAttribute('sessionIndex') : null;
                $samlAuth->logout(null, [], $token->getUserIdentifier(), $sessionIndex);
            }
        }
    }
}
