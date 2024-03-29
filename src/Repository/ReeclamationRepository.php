<?php

namespace App\Repository;

use App\Entity\Reeclamation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Reeclamation>
 *
 * @method Reeclamation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Reeclamation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Reeclamation[]    findAll()
 * @method Reeclamation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReeclamationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reeclamation::class);
    }

//    /**
//     * @return Reeclamation[] Returns an array of Reeclamation objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('r.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Reeclamation
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
