<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use phpDocumentor\Reflection\Types\Boolean;

/**
 * @extends ServiceEntityRepository<User>
 *
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }
//
//    /**
//     * Récupère les utilisateurs dont au moins un champ est vide.
//     *
//     * @return User[]
//     */
    public function findIncompleteUsers(): array
    {
        return $this->createQueryBuilder('u')
            ->where('u.age IS NULL OR u.phoneNumber IS NULL') // Vérifiez si au moins un champ est vide
            // Ajoutez d'autres conditions pour vérifier d'autres champs si nécessaire
            ->getQuery()
            ->getResult();
    }


}
