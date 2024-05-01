<?php

namespace App\Repository;

use App\Entity\Reponse;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Reponse>
 *
 * @method Reponse|null find($id, $lockMode = null, $lockVersion = null)
 * @method Reponse|null findOneBy(array $criteria, array $orderBy = null)
 * @method Reponse[]    findAll()
 * @method Reponse[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReponseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reponse::class);
    }

    /**
     * Fetches the response associated with a specific reclamation by its ID.
     *
     * @param int $reclamationId The ID of the reclamation.
     * @return Reponse|null Returns the Reponse object or null if not found.
     */
    public function findByReclamationId(int $reclamationId): ?Reponse
    {
        return $this->createQueryBuilder('r')
            ->innerJoin('r.reclamation', 'rec')
            ->where('rec.id = :id')
            ->setParameter('id', $reclamationId)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
