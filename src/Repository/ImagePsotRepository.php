<?php

namespace App\Repository;

use App\Entity\ImagePsot;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ImagePsot>
 *
 * @method ImagePsot|null find($id, $lockMode = null, $lockVersion = null)
 * @method ImagePsot|null findOneBy(array $criteria, array $orderBy = null)
 * @method ImagePsot[]    findAll()
 * @method ImagePsot[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ImagePsotRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ImagePsot::class);
    }

//    /**
//     * @return ImagePsot[] Returns an array of ImagePsot objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('i')
//            ->andWhere('i.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('i.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?ImagePsot
//    {
//        return $this->createQueryBuilder('i')
//            ->andWhere('i.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
