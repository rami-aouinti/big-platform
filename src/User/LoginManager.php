<?php

declare(strict_types=1);

namespace App\User;

use App\Admin\Auth\Security\UserChecker;
use App\Crm\Transport\Event\UserInteractiveLoginEvent;
use App\User\Domain\Entity\User;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Http\Session\SessionAuthenticationStrategyInterface;

final class LoginManager
{
    public function __construct(
        private TokenStorageInterface $tokenStorage,
        private UserChecker $userChecker,
        private SessionAuthenticationStrategyInterface $sessionStrategy,
        private RequestStack $requestStack,
        private EventDispatcherInterface $eventDispatcher,
    ) {
    }

    public function logInUser(User $user, Response $response = null)
    {
        $this->userChecker->checkPreAuth($user);

        $token = $this->createToken('secured_area', $user);
        $request = $this->requestStack->getCurrentRequest();

        if ($request !== null) {
            $this->sessionStrategy->onAuthentication($request, $token);
        }

        $this->tokenStorage->setToken($token);

        $this->eventDispatcher->dispatch(new UserInteractiveLoginEvent($user));
    }

    private function createToken(string $firewall, User $user): UsernamePasswordToken
    {
        return new UsernamePasswordToken($user, $firewall, $user->getRoles());
    }
}
