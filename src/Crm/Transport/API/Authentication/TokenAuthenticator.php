<?php

declare(strict_types=1);

namespace App\Crm\Transport\API\Authentication;

use App\Crm\Domain\Repository\ApiUserRepository;
use App\User\Domain\Entity\User;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\CustomCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;

final class TokenAuthenticator extends AbstractAuthenticator
{
    public const HEADER_USERNAME = 'X-AUTH-USER';
    public const HEADER_TOKEN = 'X-AUTH-TOKEN';

    public function __construct(
        private readonly ApiUserRepository $userProvider,
        private readonly PasswordHasherFactoryInterface $passwordHasherFactory
    ) {
    }

    public function supports(Request $request): bool
    {
        if (str_contains($request->getRequestUri(), '/api/')) {
            if (str_contains($request->getRequestUri(), '/api/doc')) {
                return false;
            }

            if ($request->headers->has(self::HEADER_USERNAME) && $request->headers->has(self::HEADER_TOKEN)) {
                @trigger_error('You are using deprecated API access, please upgrade your APP to use API tokens instead.', E_USER_DEPRECATED);

                return true;
            }
        }

        return false;
    }

    public function authenticate(Request $request): Passport
    {
        $credentials = $this->getCredentials($request);

        $checkCredentials = function (?string $presentedPassword, User $user) {
            if ($presentedPassword === '') {
                throw new BadCredentialsException('The presented password cannot be empty.');
            }

            if ($user->getApiToken() === null) {
                throw new BadCredentialsException('The user has no activated API account.');
            }

            if ($this->passwordHasherFactory->getPasswordHasher($user)->verify($user->getApiToken(), $presentedPassword)) {
                return true;
            }

            throw new BadCredentialsException('The presented password is invalid.');
        };

        $passport = new Passport(
            new UserBadge($credentials['username'], [$this->userProvider, 'loadUserByIdentifier']),
            new CustomCredentials(
                $checkCredentials,
                $credentials['password']
            )
        );

        $passport->addBadge(new ApiTokenUpgradeBadge($credentials['password'], $this->userProvider));

        return $passport;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): Response
    {
        $data = [
            'message' => $exception instanceof CustomUserMessageAuthenticationException ? $exception->getMessage() : 'Invalid credentials',
        ];

        return new JsonResponse($data, Response::HTTP_FORBIDDEN);
    }

    private function getCredentials(Request $request): array
    {
        $apiUser = $request->headers->get(self::HEADER_USERNAME);
        if ($apiUser === null || $apiUser === '') {
            throw new CustomUserMessageAuthenticationException('Authentication required, missing user header: ' . self::HEADER_USERNAME);
        }

        $apiToken = $request->headers->get(self::HEADER_TOKEN);
        if ($apiToken === null || $apiToken === '') {
            throw new CustomUserMessageAuthenticationException('Authentication required, missing token header: ' . self::HEADER_TOKEN);
        }

        return [
            'username' => $apiUser,
            'password' => $apiToken,
        ];
    }
}
