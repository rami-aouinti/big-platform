<?php

declare(strict_types=1);

namespace App\Crm\Domain\Repository;

use App\Role\Domain\Entity\Role;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Exception\ORMException;

/**
 * @extends \Doctrine\ORM\EntityRepository<Role>
 * @method Role[] findAll()
 */
class RoleRepository extends EntityRepository
{
    public function saveRole(Role $role)
    {
        $entityManager = $this->getEntityManager();
        $entityManager->persist($role);
        $entityManager->flush();
    }

    public function deleteRole(Role $role)
    {
        $em = $this->getEntityManager();
        $em->beginTransaction();

        try {
            $em->remove($role);
            $em->flush();
            $em->commit();
        } catch (ORMException $ex) {
            $em->rollback();

            throw $ex;
        }
    }
}
