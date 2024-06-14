<?php

declare(strict_types=1);

namespace App\Crm\Transport\API\Authentication;

use App\Crm\Domain\Repository\AccessTokenRepository;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Http\AccessToken\AccessTokenHandlerInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;

final class AccessTokenHandler implements AccessTokenHandlerInterface
{
    public function __construct(
        private readonly AccessTokenRepository $accessTokenRepository
    ) {
    }

    public function getUserBadgeFrom(string $accessToken): UserBadge
    {
        $accessToken = $this->accessTokenRepository->findByToken($accessToken);

        if ($accessToken === null) {
            throw new BadCredentialsException('Invalid credentials.');
        }

        if (!$accessToken->isValid()) {
            throw new BadCredentialsException('Invalid token.');
        }

        $now = new \DateTimeImmutable();
        // record last usage only if this is the first time OR once every minute
        if ($accessToken->getLastUsage() === null || $now->getTimestamp() > $accessToken->getLastUsage()->getTimestamp() + 60) {
            $accessToken->setLastUsage($now);
            $this->accessTokenRepository->saveAccessToken($accessToken);
        }

        return new UserBadge($accessToken->getUser()->getUserIdentifier(), fn (string $userIdentifier) => $accessToken->getUser());
    }
}
