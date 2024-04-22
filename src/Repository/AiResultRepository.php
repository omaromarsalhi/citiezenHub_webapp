<?php

namespace App\Repository;

use App\Entity\AiResult;
use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<AiResult>
 *
 * @method AiResult|null find($id, $lockMode = null, $lockVersion = null)
 * @method AiResult|null findOneBy(array $criteria, array $orderBy = null)
 * @method AiResult[]    findAll()
 * @method AiResult[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AiResultRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AiResult::class);
    }

//    /**
//     * @return AiResult[] Returns an array of AiResult objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('a.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?AiResult
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }


}
