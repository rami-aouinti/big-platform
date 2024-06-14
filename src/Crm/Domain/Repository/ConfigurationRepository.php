<?php

declare(strict_types=1);

namespace App\Crm\Domain\Repository;

use App\Crm\Domain\Entity\Configuration;
use App\Crm\Transport\Form\Model\SystemConfiguration;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Exception\ORMException;

use function is_object;

/**
 * @extends EntityRepository<Configuration>
 * @internal use App\Configuration\ConfigurationService instead
 * @final
 */
class ConfigurationRepository extends EntityRepository
{
    public function saveConfiguration(Configuration $configuration): void
    {
        $entityManager = $this->getEntityManager();
        $entityManager->persist($configuration);
        $entityManager->flush();
    }

    /**
     * @return array<string, string>
     */
    public function getConfigurations(): array
    {
        $query = $this->createQueryBuilder('s')->select('s.name')->addSelect('s.value')->getQuery();
        /** @var array<int, array<'name'|'value', string>> $result */
        $result = $query->getArrayResult();

        $all = [];
        foreach ($result as $row) {
            $all[$row['name']] = $row['value'];
        }

        return $all;
    }

    public function saveSystemConfiguration(SystemConfiguration $model): void
    {
        $em = $this->getEntityManager();
        $em->beginTransaction();

        try {
            foreach ($model->getConfiguration() as $configuration) {
                $entity = $this->findOneBy([
                    'name' => $configuration->getName(),
                ]);
                $value = $configuration->getValue();

                if ($value === null && $entity !== null) {
                    $em->remove($entity);

                    continue;
                }

                if ($entity === null) {
                    $entity = new Configuration();
                    $entity->setName($configuration->getName());
                }

                // allow to use entity types
                if (is_object($value) && method_exists($value, 'getId')) {
                    $value = $value->getId();
                }

                $entity->setValue($value);

                $em->persist($entity);
            }

            $em->flush();
            $em->commit();
        } catch (ORMException $ex) {
            $em->rollback();

            throw $ex;
        }
    }
}
