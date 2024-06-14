<?php

declare(strict_types=1);

namespace App\Crm\Domain\Repository;

use App\Crm\Application\Service\Invoice\AccessToken;
use App\User\Domain\Entity\User;
use Doctrine\ORM\EntityRepository;

/**
 * @extends EntityRepository<AccessToken>
 */
class AccessTokenRepository extends EntityRepository
{
    public function findByToken(string $token): ?AccessToken
    {
        return $this->findOneBy([
            'token' => $token,
        ]);
    }

    /**
     * @return array<AccessToken>
     */
    public function findForUser(User $user): array
    {
        return $this->findBy([
            'user' => $user,
        ]);
    }

    public function saveAccessToken(AccessToken $accessToken): void
    {
        $entityManager = $this->getEntityManager();
        $entityManager->persist($accessToken);
        $entityManager->flush();
    }

    public function deleteAccessToken(AccessToken $accessToken): void
    {
        $entityManager = $this->getEntityManager();
        $entityManager->remove($accessToken);
        $entityManager->flush();
    }
}
