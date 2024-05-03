<?php

namespace App\Repository;

use App\Entity\Chat;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Chat>
 *
 * @method Chat|null find($id, $lockMode = null, $lockVersion = null)
 * @method Chat|null findOneBy(array $criteria, array $orderBy = null)
 * @method Chat[]    findAll()
 * @method Chat[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ChatRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Chat::class);
    }

//    /**
//     * @return Chat[] Returns an array of Chat objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Chat
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }


    /**
     * @return Chat[] Returns an array of Chat objects
     */
    public function findByReciverOrSender($sender,$reciver): array
    {
        return $this->createQueryBuilder('c')
            ->andWhere('(c.sender = :val1 and c.reciver = :val2) or (c.sender = :val2 and c.reciver = :val1)')
            ->setParameter('val1', $sender)
            ->setParameter('val2', $reciver)
            ->getQuery()
            ->getResult()
        ;
    }

    public function updateChatState($reciver)
    {
        return $this->createQueryBuilder('c')
            ->update('App\Entity\Chat', 't')
            ->set('t.msgState',1)
            ->andWhere('t.reciver = :val1 ')
            ->setParameter('val1', $reciver)
            ->getQuery()
            ->getResult();
    }



    public function selectLastMessage($sender): array
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.sender = :val')
            ->setParameter('val', $sender)
            ->orderBy('c.idChat', 'DESC')
            ->setMaxResults(2)
            ->getQuery()
            ->getResult()
        ;
    }
}
