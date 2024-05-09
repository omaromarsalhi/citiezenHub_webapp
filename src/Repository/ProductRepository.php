<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Product>
 *
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

//    /**
//     * @return Product[] Returns an array of Product objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }


//    public function findOneBySomeField($value): ?Product
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }


    /**
     * @return Product[] Returns an array of Product objects
     */
    public function findByIdUser($value): array
    {
        return $this->createQueryBuilder('p')
            ->select('p.idProduct')
            ->andWhere('p.user = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getResult();
    }


    public function findMinMaxPrices(): array
    {
        $queryBuilder = $this->createQueryBuilder('p');

        // Select the minimum and maximum price
        $queryBuilder
            ->select('MIN(p.price) AS minPrice', 'MAX(p.price) AS maxPrice')
            ->andWhere("p.state = 'verified' ")
            ->getQuery();

        // Execute the query and get the result
        $result = $queryBuilder->getQuery()->getSingleResult();

        // Access the min and max prices
        $minPrice = $result['minPrice'];
        $maxPrice = $result['maxPrice'];

        return ['minPrice' => $minPrice, 'maxPrice' => $maxPrice];
    }

    /**
     * @return Product[] Returns an array of Product objects
     */
    public function findByPrice($min, $max): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere("p.state = 'verified' AND ( p.price BETWEEN :min AND :max )")
            ->setParameter('min', $min)
            ->setParameter('max', $max)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Product[] Returns an array of Product objects
     */
    public function findByPriceTest($filterData): array
    {
        $qb = $this->createQueryBuilder('p');


        $qb->andWhere('p.state = :value')
            ->setParameter('value', 'verified');


        if ($filterData['price']['allPrices'] === "false") {
            if ($filterData['priceIntervale']['min']) {
                $qb->andWhere('p.price >= :minPrice')
                    ->setParameter('minPrice', $filterData['priceIntervale']['min']);
            }
            if ($filterData['priceIntervale']['max']) {
                $qb->andWhere('p.price <= :maxPrice')
                    ->setParameter('maxPrice', $filterData['priceIntervale']['max']);
            }
        }

        if ($filterData['category']['food'] === "true") {
            $qb->andWhere('p.category = :category')
                ->setParameter('category', 'food');
        }

        if ($filterData['category']['sports'] === "true") {
            $qb->andWhere('p.category = :category')
                ->setParameter('category', 'sports');
        }

        if ($filterData['category']['entertainment'] === "true") {
            $qb->andWhere('p.category = :category')
                ->setParameter('category', 'entertainment');
        }

        if ($filterData['category']['realEstate'] === "true") {
            $qb->andWhere('p.category = :category')
                ->setParameter('category', 'realEstate');
        }

        if ($filterData['category']['vehicle'] === "true") {
            $qb->andWhere('p.category = :category')
                ->setParameter('category', 'vehicle');
        }

        if ($filterData['price']['asc'] === "true") {
            $qb->orderBy('p.price', 'ASC');
        }

        if ($filterData['price']['desc'] === "true") {
            $qb->orderBy('p.price', 'DESC');
        }

        if ($filterData['datetime']['today'] === "true") {
            $today = new \DateTime('today');
            $qb->andWhere('p.timestamp >= :date ')
                ->setParameter('date', $today);
        }

        if ($filterData['datetime']['thisWeek'] === "true") {
            $lastWeek = new \DateTime('-1 week');
            $qb->andWhere('p.timestamp >= :date ')
                ->setParameter('date', $lastWeek);
        }

        if ($filterData['datetime']['thisMonth'] === "true") {
            $lastMonth = new \DateTime('-1 month');
            $qb->andWhere('p.timestamp >= :date ')
                ->setParameter('date', $lastMonth);
        }


        return $qb->getQuery()->getResult();
    }

}
