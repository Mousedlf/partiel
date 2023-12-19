<?php

namespace App\Repository;

use App\Entity\AdminPrivateEvent;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<AdminPrivateEvent>
 *
 * @method AdminPrivateEvent|null find($id, $lockMode = null, $lockVersion = null)
 * @method AdminPrivateEvent|null findOneBy(array $criteria, array $orderBy = null)
 * @method AdminPrivateEvent[]    findAll()
 * @method AdminPrivateEvent[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AdminPrivateEventRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AdminPrivateEvent::class);
    }

//    /**
//     * @return AdminPrivateEvent[] Returns an array of AdminPrivateEvent objects
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

//    public function findOneBySomeField($value): ?AdminPrivateEvent
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
