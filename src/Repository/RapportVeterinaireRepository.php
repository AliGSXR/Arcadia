<?php

namespace App\Repository;

use App\Entity\RapportVeterinaire;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<RapportVeterinaire>
 */
class RapportVeterinaireRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RapportVeterinaire::class);
    }

    public function findByCriteria(?string $date, ?string $animal)
    {
        $qb = $this->createQueryBuilder('r');

        if ($date) {
            $qb->andWhere('r.date = :date')
                ->setParameter('date', $date);
        }

        if ($animal) {
            $qb->andWhere('r.animal = :animal')
                ->setParameter('animal', $animal);
        }

        return $qb->getQuery()->getResult();
    }
}
